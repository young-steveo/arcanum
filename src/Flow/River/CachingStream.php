<?php

declare(strict_types=1);

namespace Arcanum\Flow\River;

use Psr\Http\Message\StreamInterface;

class CachingStream implements StreamInterface, \Stringable, Copyable
{
    /**
     * Number of bytes to skip reading due to a previous write.
     */
    protected int $bytesToSkip = 0;

    protected StreamInterface $remote;
    protected StreamInterface $local;

    protected function __construct(
        StreamInterface $remote,
        StreamInterface $local = null,
    ) {
        $this->remote = $remote;
        if ($local === null) {
            $pointer = fopen('php://temp', 'w+');
            // @codeCoverageIgnoreStart
            if ($pointer === false) {
                throw new \RuntimeException('Could not open temporary stream');
            }
            // @codeCoverageIgnoreEnd
            $local = new Stream(StreamResource::wrap($pointer));
        }
        $this->local = $local;
    }

    public static function fromStream(StreamInterface $remote): StreamInterface
    {
        return new self($remote);
    }

    public static function fromStreamWithCache(StreamInterface $remote, StreamInterface $local): StreamInterface
    {
        return new self($remote, $local);
    }

    public function getSize(): ?int
    {
        $remoteSize = $this->remote->getSize();

        if ($remoteSize === null) {
            return null;
        }

        $localSize = $this->local->getSize();

        if ($localSize === null) {
            return null;
        }

        return max($remoteSize, $localSize);
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        $byte = match ($whence) {
            SEEK_SET => $offset,
            SEEK_CUR => $this->tell() + $offset,
            SEEK_END => ($this->remote->getSize() ?? $this->consumeEverything()) + $offset,
            default => throw new \InvalidArgumentException('Invalid whence'),
        };

        $diff = $byte - $this->local->getSize();

        if ($diff <= 0) {
            // since there is no difference, we can just seek the local stream.
            $this->local->seek($byte);
            return;
        }

        // otherwise, we need to read until we get to at least the number of
        // bytes requested, or we reach the end of the stream.
        //
        // this allows us to cache even when seeking.
        while ($diff > 0 && !$this->remote->eof()) {
            $this->read($diff);
            $diff = $byte - $this->local->getSize();
        }
    }

    public function read(int $length): string
    {
        // first read local stream
        $data = $this->local->read($length);
        $remaining = $length - strlen($data);

        // if we have read all the data we need, return it.
        if ($remaining <= 0) {
            return $data;
        }

        // otherwise, read the remaining data from the remote stream.
        // if data was written to the local stream in a position that would
        // have been filled by the remote stream, we skip that data.
        //
        // this emulates overwriting the data, which is what other PHP stream
        // wrappers do.
        $remoteData = $this->remote->read($remaining + $this->bytesToSkip);

        if ($this->bytesToSkip) {
            $remoteLength = strlen($remoteData);
            $remoteData = substr($remoteData, $this->bytesToSkip);
            $this->bytesToSkip = max(0, $this->bytesToSkip - $remoteLength);
        }

        $data .= $remoteData;
        $this->local->write($remoteData);

        return $data;
    }

    public function write(string $string): int
    {
        $overflow = strlen($string) + $this->tell() - $this->remote->tell();
        if ($overflow > 0) {
            $this->bytesToSkip += $overflow;
        }

        return $this->local->write($string);
    }

    public function eof(): bool
    {
        return $this->local->eof() && $this->remote->eof();
    }

    public function close(): void
    {
        $this->local->close();
        $this->remote->close();
    }

    public function detach()
    {
        $this->remote->detach();
        return $this->local->detach();
    }

    public function tell(): int
    {
        return $this->local->tell();
    }

    public function isSeekable(): bool
    {
        return $this->local->isSeekable();
    }

    public function isWritable(): bool
    {
        return $this->local->isWritable();
    }

    public function isReadable(): bool
    {
        return $this->local->isReadable();
    }

    public function getContents(): string
    {
        return $this->local->getContents();
    }

    public function getMetadata($key = null)
    {
        return $this->local->getMetadata($key);
    }

    public function __toString(): string
    {
        return $this->getContents();
    }

    /**
     * Cache all data from the remote stream by copying it to /dev/null.
     *
     * @param resource|null|false $pointer
     */
    public function consumeEverything($pointer = null): int
    {
        $pointer ??= fopen('/dev/null', 'w');
        if ($pointer === false) {
            throw new \RuntimeException('Could not open pointer to resource for target stream');
        }
        $devNull = StreamResource::wrap($pointer);
        $this->copyTo(new Stream($devNull));
        return $this->tell();
    }

    public function copyTo(StreamInterface $output): void
    {
        $bytes = 8192;
        while (!$this->remote->eof()) {
            if (!$output->write($this->read($bytes))) {
                break;
            }
        }
    }
}

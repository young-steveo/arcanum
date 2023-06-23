<?php

declare(strict_types=1);

namespace Arcanum\Flow\River;

use Psr\Http\Message\StreamInterface;

/**
 * @phpstan-type MetaDataKey 'timed_out'|'blocked'|'eof'|'unread_bytes'|'stream_type'|'wrapper_type'|'wrapper_data'|'mode'|'seekable'|'uri'|'crypto'
 * @phpstan-type MetaData array{
 *   timed_out: bool,
 *   blocked: bool,
 *   eof: bool,
 *   unread_bytes: int,
 *   stream_type: string,
 *   wrapper_type: string,
 *   wrapper_data?: mixed,
 *   mode: string,
 *   seekable: bool,
 *   uri: string,
 *   crypto?: array{
 *     protocol: string,
 *     cipher_name: string,
 *     cipher_bits: int,
 *     cipher_version: string
 *   }
 * }
 */
class Stream implements Copyable, \Stringable
{
    private const READABLE_MODES = '/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/';
    private const WRITABLE_MODES = '/a|w|r\+|rb\+|rw|x|c/';

    protected bool $seekable;
    protected bool $readable;
    protected bool $writable;
    protected string $uri;

    /**
     * Construct a Stream.
     */
    public function __construct(
        protected ResourceWrapper $source,
        protected int|null $size = null,
    ) {
        if (!$this->source->isResource()) {
            throw new InvalidSource('Stream source must be a live resource');
        }

        $meta = $this->getMetadata();
        if (is_array($meta)) {
            $this->seekable = $meta['seekable'];
            $this->readable = (bool) preg_match(self::READABLE_MODES, $meta['mode']);
            $this->writable = (bool) preg_match(self::WRITABLE_MODES, $meta['mode']);
            $this->uri = $meta['uri'];
        }
    }

    /**
     * Close the stream when the object is destroyed.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Return the stream as a string.
     */
    public function __toString(): string
    {
        try {
            if ($this->seekable) {
                $this->rewind();
            }
            return $this->getContents();
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * Returns whether or not the stream is readable.
     */
    public function isReadable(): bool
    {
        return $this->readable;
    }

    /**
     * Returns whether or not the stream is writable.
     */
    public function isWritable(): bool
    {
        return $this->writable;
    }

    /**
     * Returns whether or not the stream is seekable.
     */
    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     */
    public function eof(): bool
    {
        if (!isset($this->source)) {
            throw new DetachedSource('Stream is detached');
        }

        return $this->source->feof();
    }

    /**
     * Returns the current position of the file read/write pointer
     */
    public function tell(): int
    {
        if (!isset($this->source)) {
            throw new DetachedSource('Stream is detached');
        }

        $position = $this->source->ftell();
        if ($position === false) {
            throw new UnreadableStream('Unable to determine stream position');
        }

        return $position;
    }

    /**
     * Rewind the stream to the beginning.
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->source)) {
            throw new DetachedSource('Stream is detached');
        }

        if (!$this->seekable) {
            throw new UnseekableStream('Cannot seek a non-seekable stream');
        }

        if ($this->source->fseek($offset, $whence) === -1) {
            throw new UnseekableStream("Unable to seek to stream position $offset with whence $whence");
        }
    }


    /**
     * Close the stream and any underlying resources.
     */
    public function close(): void
    {
        // if $this->source is not set, the stream has already been closed
        if (!isset($this->source)) {
            return;
        }

        // is_resource is used here to avoid a warning when the stream is already closed
        if ($this->source->isResource()) {
            $this->source->fclose();
        }

        $this->detach();
    }

    /**
     * Detach the stream from any underlying resources, if any.
     *
     * This will render the stream unusable.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        if (!isset($this->source)) {
            return null;
        }

        $source = $this->source;
        unset($this->source);
        $this->size = 0;
        $this->seekable = false;
        $this->readable = false;
        $this->writable = false;
        $this->uri = '';

        return $source->export();
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * `stream_get_meta_data()` function.
     *
     * @param null|MetaDataKey $key Specific metadata to retrieve.
     * @return mixed|MetaData
     */
    public function getMetadata(string|null $key = null): mixed
    {
        if (!isset($this->source)) {
            throw new DetachedSource('Cannot get metadata from a detached resource');
        }

        $meta = $this->source->streamGetMetaData();
        if (!$key) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    public function getContents(): string
    {
        if (!isset($this->source)) {
            throw new DetachedSource('Cannot get contents from a detached resource');
        }

        if (!$this->readable) {
            throw new UnreadableStream('Cannot get contents from an unreadable resource');
        }
        try {
            $contents = $this->source->streamGetContents();
            if ($contents === false) {
                throw new \RuntimeException('Could not read stream');
            }
        } catch (\Throwable $e) {
            throw new UnreadableStream('Could not read stream', 0, $e);
        }
        return $contents;
    }

    public function getSize(): ?int
    {
        if (!isset($this->source)) {
            throw new DetachedSource('Cannot get the size of a detached stream');
        }

        if ($this->size) {
            return $this->size;
        }

        if ($this->uri) {
            $this->source->clearstatcache(true, $this->uri);
        }

        $stats = $this->source->fstat();
        if ($stats && $stats['size'] !== false) {
            $this->size = $stats['size'];
            return $this->size;
        }

        return null;
    }

    /**
     * Read data from the stream.
     */
    public function read(int $length): string
    {
        if (!isset($this->source)) {
            throw new DetachedSource('Stream is detached');
        }

        if (!$this->readable) {
            throw new UnreadableStream('Stream is not readable');
        }

        if ($length < 0) {
            throw new \InvalidArgumentException('Length to read must be greater than or equal to zero');
        }

        if ($length === 0) {
            return '';
        }

        try {
            $data = $this->source->fread($length);
        } catch (\Throwable $e) {
            throw new UnreadableStream('Unable to read from stream', 0, $e);
        }
        if ($data === false) {
            throw new UnreadableStream('Unable to read from stream');
        }

        return $data;
    }

    /**
     * Write data to the stream.
     */
    public function write(string $string): int
    {
        if (!isset($this->source)) {
            throw new DetachedSource('Cannot write to a detached stream');
        }

        if (!$this->writable) {
            throw new UnwritableStream('Cannot write to a non-writable stream');
        }

        $this->size = null;

        try {
            $result = $this->source->fwrite($string);
        } catch (\Throwable $e) {
            throw new UnwritableStream('Unable to write to stream', 0, $e);
        }
        if ($result === false) {
            throw new UnwritableStream('Unable to write to stream');
        }

        return $result;
    }

    public function copyTo(StreamInterface $output): void
    {
        $bytes = 8192;
        while (!$this->eof()) {
            if (!$output->write($this->read($bytes))) {
                break;
            }
        }
    }
}

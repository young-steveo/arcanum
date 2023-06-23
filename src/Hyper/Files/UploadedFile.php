<?php

declare(strict_types=1);

namespace Arcanum\Hyper\Files;

use Arcanum\Flow\River\LazyResource;
use Arcanum\Flow\River\ResourceWrapper;
use Arcanum\Flow\River\Stream;
use Arcanum\Flow\River\StreamResource;
use Arcanum\Flow\River\Copyable;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    private string|null $file;

    private bool $moved = false;

    private StreamInterface|null $stream;

    /**
     * @param StreamInterface|string|resource|ResourceWrapper|null $file
     */
    public function __construct(
        $file,
        private string $mode,
        private Error $error = Error::UPLOAD_ERR_OK,
        private int|null $size = null,
        private string|null $clientFilename = null,
        private string|null $clientMediaType = null
    ) {
        if ($this->error->isOK() && $file !== null) {
            $this->marshallFile($file);
        }
    }

    /**
     * @param array{
     *   tmp_name?: mixed,
     *   error?: int|null,
     *   size?: int|null,
     *   name?: string|null,
     *   type?: string|null,
     * } $spec
     * @return UploadedFileInterface|array<string, mixed>
     */
    public static function fromSpec(array $spec): UploadedFileInterface|array
    {
        if (is_array($spec['tmp_name'] ?? null)) {
            return self::normalizeSpec($spec);
        }
        $error = Error::fromErrorCode($spec['error'] ?? \UPLOAD_ERR_OK);
        $size = $spec['size'] ?? null;
        $clientFilename = $spec['name'] ?? null;
        $clientMediaType = $spec['type'] ?? null;
        $file = $spec['tmp_name'] ?? null;
        if (!is_string($file)) {
            throw new InvalidFile('Invalid file provided for UploadedFile::fromSpec');
        }
        return new self($file, 'r+b', $error, $size, $clientFilename, $clientMediaType);
    }

    /**
     * @param array<string, mixed> $spec
     * @return array<string, mixed>
     */
    protected static function normalizeSpec(array $spec): array
    {
        $normalized = [];

        /** @var array<string,mixed> $tmpName */
        $tmpName = $spec['tmp_name'] ?? [];
        foreach (array_keys($tmpName) as $key) {
            $normalized[$key] = self::fromSpec([
                'tmp_name' => $tmpName[$key] ?? null,
                'size' => !is_array($spec['size']) ? null : ($spec['size'][$key] ?? null),
                'error' => !is_array($spec['error']) ? null : ($spec['error'][$key] ?? null),
                'name' => !is_array($spec['name']) ? null : ($spec['name'][$key] ?? null),
                'type' => !is_array($spec['type']) ? null : ($spec['type'][$key] ?? null),
            ]);
        }

        return $normalized;
    }

    /**
     * @param StreamInterface|string|resource|ResourceWrapper $file
     * @throws InvalidFile
     */
    protected function marshallFile($file): void
    {
        if (is_string($file)) {
            $this->file = $file;
            return;
        }

        $this->stream = match (true) {
            $file instanceof StreamInterface => $file,
            is_resource($file) => new Stream(StreamResource::wrap($file)),
            $file instanceof ResourceWrapper => new Stream($file)
        };
    }

    public function getStream(): StreamInterface
    {
        if (!$this->error->isOK()) {
            throw new InvalidFile('Cannot retrieve stream due to upload error');
        }
        if (isset($this->stream)) {
            return $this->stream;
        }
        if (isset($this->file)) {
            return new Stream(LazyResource::for($this->file, $this->mode));
        }
        throw new InvalidFile('No file or stream available');
    }

    /**
     * @param non-empty-string $targetPath
     */
    public function moveTo(string $targetPath): void
    {
        if (empty($targetPath)) {
            throw new InvalidFile('Target path cannot be empty');
        }
        if ($this->moved) {
            throw new InvalidFile('File has already been moved');
        }
        if (!$this->error->isOK()) {
            throw new InvalidFile('Cannot move file due to upload error');
        }
        $this->moved = isset($this->file)
            ? $this->nativeMove($targetPath)
            : $this->streamMove($targetPath);

        if (!$this->moved) {
            throw new \RuntimeException('Unable to move file');
        }
    }

    protected function nativeMove(string $targetPath): bool
    {
        return \PHP_SAPI === 'cli'
            ? rename($this->file ?? '', $targetPath)
            : move_uploaded_file($this->file ?? '', $targetPath);
    }

    protected function streamMove(string $targetPath): bool
    {
        $stream = $this->getStream();
        $target = new Stream(LazyResource::for($targetPath, 'w'));
        if ($stream instanceof Copyable) {
            $meta = $stream->getMetadata();
            $stream->copyTo($target);
            if (
                is_array($meta) &&
                isset($meta['uri']) &&
                $meta['uri'] !== $targetPath &&
                file_exists($meta['uri'])
            ) {
                unlink($meta['uri']);
            }
            return true;
        }
        // manually copy stream
        while (!$stream->eof()) {
            $target->write($stream->read(4096));
        }
        return $stream->eof();
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error->value;
    }

    public function getClientFilename(): string|null
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): string|null
    {
        return $this->clientMediaType;
    }
}

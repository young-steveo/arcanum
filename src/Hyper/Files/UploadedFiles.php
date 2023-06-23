<?php

declare(strict_types=1);

namespace Arcanum\Hyper\Files;

use Psr\Http\Message\UploadedFileInterface;

/**
 * Wrapper for PHP's $_FILES superglobal.
 *
 * This allows the ServerRequest class to confirm to PSR-7's
 * ServerRequestInterface::getUploadedFiles() reccomendations
 */
final class UploadedFiles
{
    /**
     * @var array<string, mixed>
     */
    private array $originalFiles;

    /**
     * @var array<string, mixed>
     */
    private array $normalizedFiles = [];

    /**
     * @param array<string, mixed> $files Should conform to $_FILES superglobal
     */
    private function __construct(array $files)
    {
        $this->originalFiles = $files;
    }

    public static function fromSuperGlobal(): self
    {
        return new self($_FILES);
    }

    /**
     * @param array<string, mixed> $files Should conform to $_FILES superglobal
     */
    public static function fromArray(array $files): self
    {
        return new self($files);
    }

    /**
     * @param array<string, mixed> $files Should conform to $_FILES superglobal
     * @return array<string, mixed>
     */
    private static function normalize(array $files): array
    {
        $normalized = [];
        foreach ($files as $key => $value) {
            $normalized[$key] = match (true) {
                $value instanceof UploadedFileInterface => $value,
                is_array($value) && isset($value['tmp_name']) => UploadedFile::fromSpec($value),
                is_array($value) => static::normalize($value),
                default => throw new \InvalidArgumentException('Invalid value in files specification'),
            };
        }
        return $normalized;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if (empty($this->normalizedFiles)) {
            $this->normalizedFiles = static::normalize($this->originalFiles);
        }
        return $this->normalizedFiles;
    }
}

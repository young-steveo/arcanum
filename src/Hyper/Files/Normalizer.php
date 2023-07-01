<?php

declare(strict_types=1);

namespace Arcanum\Hyper\Files;

use Psr\Http\Message\UploadedFileInterface;

final class Normalizer
{
    /**
     * @param string|array<string|int,mixed> $tmpName
     * @param int|null|array<string|int,mixed> $error
     * @param int|null|array<string|int,mixed> $size
     * @param string|null|array<string|int,mixed> $clientFilename
     * @param string|null|array<string|int,mixed> $clientMediaType
     * @return UploadedFileInterface|array<string, mixed>
     */
    public static function fromSpec(
        string|array $tmpName,
        int|null|array $error = null,
        int|null|array $size = null,
        string|null|array $clientFilename = null,
        string|null|array $clientMediaType = null
    ): UploadedFileInterface|array {
        if (static::containsArray($tmpName, $error, $size, $clientFilename, $clientMediaType)) {
            return static::normalizeSpec([
                'tmp_name' => $tmpName,
                'error' => $error,
                'size' => $size,
                'name' => $clientFilename,
                'type' => $clientMediaType,
            ]);
        }

        $error = Error::from((int)($error ?? \UPLOAD_ERR_OK));

        /** @var string $tmpName */
        /** @var int|null $size */
        /** @var string|null $clientFilename */
        /** @var string|null $clientMediaType */
        return new UploadedFile($tmpName, 'r+b', $error, $size, $clientFilename, $clientMediaType);
    }

    /**
     * Check if any of the given parameters is an array.
     *
     * @param mixed ...$params
     */
    protected static function containsArray(mixed ...$params): bool
    {
        foreach ($params as $param) {
            if (is_array($param)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array<string, mixed> $spec
     * @return array<string, mixed>
     */
    protected static function normalizeSpec(array $spec): array
    {
        $normalized = [];

        /** @var array<string,string|array<string|int,mixed>> $tmpName */
        $tmpName = $spec['tmp_name'] ?? [];
        foreach (array_keys($tmpName) as $key) {
            $normalized[$key] = static::fromSpec(
                tmpName: $tmpName[$key] ?? '',
                size: !is_array($spec['size']) ? null : ($spec['size'][$key] ?? null),
                error: !is_array($spec['error']) ? null : ($spec['error'][$key] ?? null),
                clientFilename: !is_array($spec['name']) ? null : ($spec['name'][$key] ?? null),
                clientMediaType: !is_array($spec['type']) ? null : ($spec['type'][$key] ?? null),
            );
        }

        return $normalized;
    }
}

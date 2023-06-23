<?php

declare(strict_types=1);

namespace Arcanum\Flow\River;

use Psr\Http\Message\StreamInterface;

final class Bank
{
    public static function copyTo(StreamInterface $source, StreamInterface $target): void
    {
        if ($source instanceof Copyable) {
            $source->copyTo($target);
        } else {
            while (!$source->eof()) {
                $target->write($source->read(4096));
            }
        }
    }

    public static function deleteIfMoved(StreamInterface $stream, string $targetPath): void
    {
        $meta = $stream->getMetadata();
        if (
            is_array($meta) &&
            isset($meta['uri']) &&
            $meta['uri'] !== $targetPath &&
            file_exists($meta['uri'])
        ) {
            unlink($meta['uri']);
        }
    }
}

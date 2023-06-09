<?php

declare(strict_types=1);

namespace Arcanum\Flow\River;

final class StreamExporter
{
    public function __construct(private StreamResource $stream)
    {
        if (!$stream->isResource()) {
            throw new InvalidSource('Stream source must be a live resource');
        }
    }

    /**
     * Return the contents of the stream as a string.
     *
     * @throws UnreadableStream
     */
    public function getContents(): string
    {
        try {
            $contents = $this->stream->streamGetContents();
            if ($contents === false) {
                throw new \RuntimeException('Could not read stream');
            }
        } catch (\Throwable $e) {
            throw new UnreadableStream('Could not read stream', 0, $e);
        }
        return $contents;
    }
}

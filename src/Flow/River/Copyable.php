<?php

declare(strict_types=1);

namespace Arcanum\Flow\River;

use Psr\Http\Message\StreamInterface;

interface Copyable
{
    /**
     * Copy the stream to an output stream.
     */
    public function copyTo(StreamInterface $output): void;
}

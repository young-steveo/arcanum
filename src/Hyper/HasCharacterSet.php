<?php

declare(strict_types=1);

namespace Arcanum\Hyper;

interface HasCharacterSet
{
    /**
     * The default character set.
     */
    public const UTF8 = 'UTF-8';

    /**
     * Get the character set.
     */
    public function getCharacterSet(): string;
}

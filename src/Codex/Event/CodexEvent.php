<?php

declare(strict_types=1);

namespace Arcanum\Codex\Event;

interface CodexEvent
{
    /**
     * Get the class name.
     *
     * @return class-string
     */
    public function className(): string;

    /**
     * Get the class.
     *
     * Will return null if the class is not yet resolved.
     */
    public function class(): object|null;
}

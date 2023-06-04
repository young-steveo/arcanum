<?php

declare(strict_types=1);

namespace Arcanum\Codex\Event;

final class ClassRequested extends \Arcanum\Echo\Event implements CodexEvent
{
    /**
     * @param class-string $class
     */
    public function __construct(private string $class)
    {
    }

    /**
     * Get the class name.
     *
     * @return class-string
     */
    public function className(): string
    {
        return $this->class;
    }

    /**
     * Get the class.
     *
     * Will return null if the class is not yet resolved.
     */
    public function class(): object|null
    {
        return null;
    }
}

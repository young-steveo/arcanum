<?php

declare(strict_types=1);

namespace Arcanum\Codex;

interface Specifier
{
    /**
     * Define a specification for a dependency.
     *
     * @param class-string|array<class-string> $when
     * @param string $needs Either a class name or a variable name.
     * @param mixed $give
     */
    public function specify(string|array $when, string $needs, mixed $give): void;
}

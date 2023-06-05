<?php

declare(strict_types=1);

namespace Arcanum\Flow\Continuum;

interface Progression
{
    /**
     * Single stage in a continuum.
     *
     * @param callable(): void $next
     */
    public function __invoke(object $payload, callable $next): void;
}

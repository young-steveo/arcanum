<?php

declare(strict_types=1);

namespace Arcanum\Flow;

interface Stage
{
    /**
     * Single stage in a pipeline.
     */
    public function __invoke(object $payload, callable $next = null): object|null;
}

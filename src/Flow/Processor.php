<?php

declare(strict_types=1);

namespace Arcanum\Flow;

interface Processor
{
    /**
     * Process a payload through a series of stages.
     *
     * @param callable(object, callable): (object|null) $stages
     */
    public function process(object $payload, callable ...$stages): object;
}

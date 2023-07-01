<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

interface DecoratorRegistry
{
    /**
     * Register a decorator.
     *
     * @param string $serviceName
     * @param callable(object): object $decorator
     */
    public function decorator(string $serviceName, callable $decorator): void;
}

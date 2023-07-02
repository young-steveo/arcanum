<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

/**
 * Decorator Registry Interface
 * ----------------------------
 *
 * The decorator registry interface defines the methods required to register
 * decorators with the application container.
 */
interface DecoratorRegistry
{
    /**
     * Register a decorator.
     *
     * @template T of object
     * @param string $serviceName
     * @param callable(T): T $decorator
     */
    public function decorator(string $serviceName, callable $decorator): void;
}

<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

final class PrototypeProvider extends Provider
{
    /**
     * PrototypeProvider is used by the framework to register prototype services.
     */
    private function __construct(private \Closure $factory)
    {
    }

    /**
     * Create a PrototypeProvider from a factory closure.
     */
    public static function fromFactory(\Closure $factory): static
    {
        return new static($factory);
    }

    /**
     * Provide a service from the factory.
     */
    public function __invoke(Container $container): mixed
    {
        return ($this->factory)($container);
    }
}

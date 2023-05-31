<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

final class SimpleProvider extends Provider
{
    /**
     * SimpleProvider is used by the framework to register simple services.
     */
    private function __construct(private \Closure $factory)
    {
    }

    /**
     * Create a SimpleProvider from a factory closure.
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

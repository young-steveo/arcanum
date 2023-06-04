<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

final class SimpleProvider extends Provider
{
    /**
     * The service that is provided by this provider.
     */
    private mixed $service;

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
    public function __invoke(Container $container): object|null
    {
        if (!isset($this->service)) {
            $this->service = ($this->factory)($container);
        }
        return $this->service;
    }
}

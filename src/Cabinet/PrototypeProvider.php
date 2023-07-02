<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

/**
 * Prototype Provider
 * ------------------
 *
 * The prototype provider is used to consistently provide prototype services
 * from a factory closure. It is used by Container in two ways:
 *
 * 1. To register prototype services with the container when `prototypeFactory`
 *    is called.
 * 2. To provide prototype services on the fly when the container is asked to
 *    provide a service that has not been previously registered.
 */
class PrototypeProvider extends Provider
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
    public static function fromFactory(\Closure $factory): self
    {
        return new self($factory);
    }

    /**
     * Provide a service from the factory.
     */
    public function __invoke(Container $container): object
    {
        return ($this->factory)($container);
    }
}

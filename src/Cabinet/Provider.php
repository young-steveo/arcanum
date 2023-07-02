<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

/**
 * Provider
 * --------
 *
 * Extend the Provider class to create your own service providers. These
 * providers can be registered with the application container to provide
 * fine-grained control over how services are created.
 */
abstract class Provider
{
    /**
     * Invoking the provider should return the service.
     */
    abstract public function __invoke(Container $container): object;
}

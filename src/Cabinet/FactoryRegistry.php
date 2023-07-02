<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

/**
 * Factory Registry Interface
 * --------------------------
 *
 * The factory registry interface defines the methods required to register
 * factories with the application container.
 */
interface FactoryRegistry
{
    /**
     * Register a service factory.
     *
     * @param string $serviceName
     */
    public function factory(string $serviceName, \Closure $factory): void;
}

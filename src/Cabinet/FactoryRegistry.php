<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

interface FactoryRegistry
{
    /**
     * Register a service factory.
     *
     * @param string $serviceName
     */
    public function factory(string $serviceName, \Closure $factory): void;
}

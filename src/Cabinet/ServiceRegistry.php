<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

interface ServiceRegistry
{
    /**
     * Register a service
     *
     * @param string $serviceName
     * @param class-string|null $implementation
     */
    public function service(string $serviceName, string|null $implementation = null): void;

    /**
     * Register a service while defining its dependencies.
     *
     * @param string $serviceName
     * @param class-string[] $dependencies
     */
    public function serviceWith(string $serviceName, array $dependencies): void;
}

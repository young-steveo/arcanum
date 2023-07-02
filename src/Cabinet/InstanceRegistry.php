<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

/**
 * Instance Registry Interface
 * ---------------------------
 *
 * The instance registry interface defines the methods required to register
 * instances with the application container.
 */
interface InstanceRegistry
{
    /**
     * Register a service instance.
     *
     * @param string $serviceName
     */
    public function instance(string $serviceName, mixed $instance): void;
}

<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

interface InstanceRegistry
{
    /**
     * Register a service instance.
     *
     * @param string $serviceName
     */
    public function instance(string $serviceName, mixed $instance): void;
}

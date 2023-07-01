<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

interface PrototypeRegistry
{
    /**
     * Register a prototype.
     *
     * @param string $serviceName
     */
    public function prototype(string $serviceName): void;

    /**
     * Register a prototype factory.
     *
     * @param string $serviceName
     */
    public function prototypeFactory(string $serviceName, \Closure $factory): void;
}

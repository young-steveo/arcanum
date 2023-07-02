<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

/**
 * Prototype Registry Interface
 * ----------------------------
 *
 * The prototype registry interface defines the methods required to register
 * prototypes with the application container.
 */
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

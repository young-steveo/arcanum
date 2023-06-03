<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

abstract class Provider
{
    /**
     * Invoking the provider should return the service.
     */
    abstract public function __invoke(Container $container): mixed;
}

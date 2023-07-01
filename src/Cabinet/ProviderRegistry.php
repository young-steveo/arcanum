<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

interface ProviderRegistry
{
    /**
     * Register a service provider.
     *
     * @param string $serviceName
     */
    public function provider(string $serviceName, Provider $provider): void;
}

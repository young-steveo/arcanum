<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

use Arcanum\Flow\Continuum\Progression;

interface MiddlewareRegistry
{
    /**
     * Register a middleware.
     *
     * @param string $serviceName
     * @param Progression $middleware
     */
    public function middleware(string $serviceName, Progression $middleware): void;
}

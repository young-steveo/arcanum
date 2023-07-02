<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

use Arcanum\Flow\Continuum\Progression;

/**
 * Middleware Registry Interface
 * -----------------------------
 *
 * The middleware registry interface defines the methods required to register
 * middleware with the application container.
 */
interface MiddlewareRegistry
{
    /**
     * Register a middleware.
     *
     * @param string $serviceName
     * @param Progression|class-string<Progression> $middleware
     */
    public function middleware(string $serviceName, string|Progression $middleware): void;
}

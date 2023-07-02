<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

use Arcanum\Codex\ClassResolver;
use Arcanum\Flow\Continuum\Progression;

/**
 * Middleare Progression
 * ---------------------
 *
 * This class is used to lazy-load any middleware that is registered via a
 * class string instead of an instance of the middleware.
 */
final class MiddlewareProgression implements \Arcanum\Flow\Continuum\Progression
{
    /**
     * @var class-string<Progression>
     */
    private string $middleware;

    /**
     * @var ClassResolver
     */
    private ClassResolver $resolver;

    /**
     * @var Progression|null
     */
    private Progression|null $instance = null;

    /**
     * @param class-string<Progression> $middleware
     */
    public function __construct(string $middleware, ClassResolver $resolver)
    {
        $this->middleware = $middleware;
        $this->resolver = $resolver;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(object $payload, callable $next): void
    {
        if ($this->instance === null) {
            $this->instance = $this->resolver->resolve($this->middleware);
        }
        $middleware = $this->instance;
        $middleware($payload, $next);
    }
}

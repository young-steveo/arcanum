<?php

declare(strict_types=1);

namespace Arcanum\Conveyor\Middleware;

use Arcanum\Flow\Continuum\Progression;

/**
 * NonPublicMethodFilter only allows objects that have zero public instance methods.
 *
 * Public static methods are allowed by this filter.
 */
final class NonPublicMethodFilter implements Progression
{
    public function __invoke(object $payload, callable $next): void
    {
        $image = new \ReflectionClass($payload);
        $methods = $image->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (!$method->isConstructor() && !$method->isStatic()) {
                $name = $image->getName();
                throw new InvalidDTO("$name has non-static methods that are public.");
            }
        }
        $next();
    }
}

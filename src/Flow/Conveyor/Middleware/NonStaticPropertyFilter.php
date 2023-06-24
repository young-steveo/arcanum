<?php

declare(strict_types=1);

namespace Arcanum\Flow\Conveyor\Middleware;

use Arcanum\Flow\Continuum\Progression;

/**
 * NonStaticPropertyFilter only allows objects that have zero static properties.
 */
final class NonStaticPropertyFilter implements Progression
{
    public function __invoke(object $payload, callable $next): void
    {
        $image = new \ReflectionClass($payload);
        $properties = $image->getProperties(\ReflectionProperty::IS_STATIC);
        if (!empty($properties)) {
            $name = $image->getName();
            throw new InvalidDTO("$name has static properties.");
        }
        $next();
    }
}

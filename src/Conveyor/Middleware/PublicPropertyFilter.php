<?php

declare(strict_types=1);

namespace Arcanum\Conveyor\Middleware;

use Arcanum\Flow\Continuum\Progression;

/**
 * PublicPropertyFilter only allows objects that have zero or more public
 * properties.
 *
 * If the object has any private or protected properties, PublicPropertyFilter
 * will throw an InvalidDTO exception.
 */
final class PublicPropertyFilter implements Progression
{
    public function __invoke(object $payload, callable $next): void
    {
        $image = new \ReflectionClass($payload);
        $properties = $image->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED);
        if (!empty($properties)) {
            $name = $image->getName();
            throw new InvalidDTO("$name has private or protected properties.");
        }
        $next();
    }
}

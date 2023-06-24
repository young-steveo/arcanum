<?php

declare(strict_types=1);

namespace Arcanum\Flow\Conveyor\Middleware;

use Arcanum\Flow\Continuum\Progression;

/**
 * ReadOnlyPropertyFilter only allows objects that have readonly properties.
 */
final class ReadOnlyPropertyFilter implements Progression
{
    public function __invoke(object $payload, callable $next): void
    {
        $image = new \ReflectionClass($payload);
        $readonly = $image->getProperties(\ReflectionProperty::IS_READONLY);
        $allProperties = $image->getProperties();
        if (count($allProperties) > count($readonly)) {
            $name = $image->getName();
            throw new InvalidDTO("$name has properties that are not readonly.");
        }
        $next();
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Flow\Conveyor\Middleware;

use Arcanum\Flow\Continuum\Progression;

/**
 * FinalFilter only allows objects that have been declared final.
 */
final class FinalFilter implements Progression
{
    public function __invoke(object $payload, callable $next): void
    {
        $image = new \ReflectionClass($payload);
        if (!$image->isFinal()) {
            $name = $image->getName();
            throw new InvalidDTO("$name is not final.");
        }
        $next();
    }
}

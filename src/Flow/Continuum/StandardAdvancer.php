<?php

declare(strict_types=1);

namespace Arcanum\Flow\Continuum;

use Arcanum\Flow\Interrupted;

class StandardAdvancer implements Advancer
{
    /**
     * Advance a payload through progressions.
     *
     * @param Progression $stages
     */
    public function advance(object $payload, Progression ...$stages): object
    {
        $stages = array_reverse($stages);
        $next = function () {
        }; // noop
        foreach ($stages as $stage) {
            $next = function () use ($payload, $stage, $next): void {
                $called = false;
                $stage($payload, function () use (&$called, $next): void {
                    $called = true;
                    $next();
                });
                if (!$called) {
                    throw new Interrupted("Continuum progression did not call next.");
                }
            };
        }
        $next();
        return $payload;
    }
}

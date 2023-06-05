<?php

declare(strict_types=1);

namespace Arcanum\Flow\Continuum;

interface Advancer
{
    /**
     * Advance a payload through progressions.
     *
     * @param Progression $stages
     */
    public function advance(object $payload, Progression ...$stages): object;
}

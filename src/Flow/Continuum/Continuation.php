<?php

declare(strict_types=1);

namespace Arcanum\Flow\Continuum;

use Arcanum\Flow\Sender;

interface Continuation extends Sender
{
    /**
     * Add a progression to a continuum.
     */
    public function add(Progression $stage): Continuation;
}

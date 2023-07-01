<?php

declare(strict_types=1);

namespace Arcanum\Ignition;

interface Terminable
{
    /**
     * Terminate the application.
     */
    public function terminate(): void;
}

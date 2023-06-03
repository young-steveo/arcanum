<?php

declare(strict_types=1);

namespace Arcanum\Echo;

use Psr\EventDispatcher\StoppableEventInterface;

abstract class Event implements StoppableEventInterface
{
    /**
     * Whether no further event listeners should be triggered.
     */
    private bool $stopped = false;

    /**
     * Stop the propagation of the event to further listeners.
     */
    public function stopPropagation(): void
    {
        $this->stopped = true;
    }

    /**
     * Whether no further event listeners should be triggered.
     */
    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }
}

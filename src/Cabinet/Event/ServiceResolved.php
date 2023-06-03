<?php

declare(strict_types=1);

namespace Arcanum\Cabinet\Event;

final class ServiceResolved extends \Arcanum\Echo\Event
{
    /**
     * @param mixed $service
     */
    public function __construct(private mixed $service)
    {
    }

    /**
     * Get the service that was resolved.
     */
    public function service(): mixed
    {
        return $this->service;
    }
}

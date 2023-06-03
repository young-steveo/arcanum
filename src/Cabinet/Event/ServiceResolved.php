<?php

declare(strict_types=1);

namespace Arcanum\Cabinet\Event;

final class ServiceResolved extends \Arcanum\Echo\Event implements CabinetEvent
{
    /**
     * @param object $service
     */
    public function __construct(private object $service)
    {
    }

    /**
     * Get the service name.
     *
     * @return class-string
     */
    public function serviceName(): string
    {
        return get_class($this->service);
    }

    /**
     * Get the service.
     *
     * Will return null if the service is not yet resolved.
     */
    public function service(): object|null
    {
        return $this->service;
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Cabinet\Event;

final class ServiceRequested extends \Arcanum\Echo\Event implements CabinetEvent
{
    /**
     * @param class-string $service
     */
    public function __construct(private string $service)
    {
    }

    /**
     * Get the service name.
     *
     * @return class-string
     */
    public function serviceName(): string
    {
        return $this->service;
    }

    /**
     * Get the service.
     *
     * Will return null if the service is not yet resolved.
     */
    public function service(): object|null
    {
        return null;
    }
}

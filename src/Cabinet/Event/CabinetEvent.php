<?php

declare(strict_types=1);

namespace Arcanum\Cabinet\Event;

interface CabinetEvent
{
    /**
     * Get the service name.
     *
     * @return class-string
     */
    public function serviceName(): string;

    /**
     * Get the service.
     *
     * Will return null if the service is not yet resolved.
     */
    public function service(): object|null;
}

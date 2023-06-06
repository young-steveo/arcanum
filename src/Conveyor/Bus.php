<?php

declare(strict_types=1);

namespace Arcanum\Conveyor;

interface Bus
{
    /**
     * Dispatch an object to a handler.
     *
     * The object to be dispatched should be a simple DTO, and the
     * return value should be a simple DTO.
     */
    public function dispatch(object $object): object;
}

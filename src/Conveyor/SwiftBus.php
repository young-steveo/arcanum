<?php

declare(strict_types=1);

namespace Arcanum\Conveyor;

use Arcanum\Cabinet\Container;

class SwiftBus implements Bus
{
    public function __construct(protected Container $container)
    {
    }

    /**
     * Dispatch an object to a handler.
     */
    public function dispatch(object $object): object
    {
        return $this->handlerFor($object)($object) ?? new EmptyDTO();
    }

    /**
     * Get the handler for an object.
     *
     * SwiftBus assumes that the handler is a callable.
     */
    protected function handlerFor(object $object): callable
    {
        /** @var callable */
        return $this->container->get($this->handlerNameFor($object));
    }

    /**
     * Get the handler name for an object.
     *
     * This is the class name of the object with the suffix "Handler".
     *
     * @return class-string
     */
    protected function handlerNameFor(object $object): string
    {
        /** @var class-string */
        return get_class($object) . 'Handler';
    }
}

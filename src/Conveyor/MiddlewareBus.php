<?php

declare(strict_types=1);

namespace Arcanum\Conveyor;

use Psr\Container\ContainerInterface;
use Arcanum\Flow\Continuum\Continuum;
use Arcanum\Flow\Continuum\Continuation;
use Arcanum\Flow\Continuum\Progression;
use Arcanum\Flow\Pipeline\Pipeline;

class MiddlewareBus implements Bus
{
    /**
     * MiddlewareBus
     */
    public function __construct(
        protected ContainerInterface $container,
        protected Continuation $dispatchFlow = new Continuum(),
        protected Continuation $responseFlow = new Continuum()
    ) {
    }

    /**
     * Add dispatch middleware to the bus.
     */
    public function before(Progression ...$middleware): void
    {
        foreach ($middleware as $layer) {
            $this->dispatchFlow = $this->dispatchFlow->add($layer);
        }
    }

    /**
     * Add response middleware to the bus.
     */
    public function after(Progression ...$middleware): void
    {
        foreach ($middleware as $layer) {
            $this->responseFlow = $this->responseFlow->add($layer);
        }
    }


    /**
     * Dispatch an object to a handler.
     */
    public function dispatch(object $object): object
    {
        return (new Pipeline())
            ->pipe($this->dispatchFlow)
            ->pipe(function (object $object) {
                return $this->handlerFor($object)($object) ?? new EmptyDTO();
            })
            ->pipe($this->responseFlow)
            ->send($object);
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

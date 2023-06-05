<?php

declare(strict_types=1);

namespace Arcanum\Echo;

use Psr\EventDispatcher\EventDispatcherInterface;

class Dispatcher implements EventDispatcherInterface
{
    public function __construct(
        private Provider $provider
    ) {
    }

    /**
     * Dispatch an event.
     */
    public function dispatch(object $event): object
    {
        $event = $this->wrap($event);

        try {
            /** @var Event */
            $event = $this->provider->listenerPipeline($event)->send($event);
        } catch (\Arcanum\Flow\Interrupted) {
        }

        return $this->unwrap($event);
    }

    /**
     * Unwrap an event.
     *
     * The PSR-14 standard requires that we return the original event
     * object.
     */
    protected function unwrap(Event $event): object
    {
        return $event instanceof UnknownEvent ? $event->payload : $event;
    }

    /**
     * Wrap an event.
     *
     * We wrap unknown events with \Arcanum\Echo\UnknownEvent. This simplifies
     * our listener logic, as we can always expect an \Arcanum\Echo\Event.
     */
    protected function wrap(object $event): Event
    {
        return $event instanceof Event ? $event : UnknownEvent::fromObject($event);
    }
}

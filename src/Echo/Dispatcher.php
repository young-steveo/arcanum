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
        if (!$event instanceof Event) {
            $event = UnknownEvent::fromObject($event);
        }

        $duplicates = [];
        foreach ($this->provider->getListenersForEvent($event) as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            // We don't want to call the same listener twice for a single event.
            // this check is needed because the same listener might be registered
            // for events that inherit from each other.
            if (in_array($listener, $duplicates, true)) {
                continue;
            }
            $duplicates[] = $listener;

            // execute the listener
            $event = $listener($event);
        }

        return $this->unwrap($event);
    }

    /**
     * Unwrap an event.
     *
     * We wrap unknown events with \Arcanum\Echo\UnknownEvent. This simplifies
     * our listener logic, as we can always expect an \Arcanum\Echo\Event.
     * However, the PSR-14 standard requires that we return the original event
     * object.
     */
    protected function unwrap(Event $event): object
    {
        return $event instanceof UnknownEvent ? $event->payload : $event;
    }
}

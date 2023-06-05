<?php

declare(strict_types=1);

namespace Arcanum\Echo;

use Psr\EventDispatcher\ListenerProviderInterface;
use Arcanum\Flow\Pipeline\Pipelayer;
use Arcanum\Flow\Pipeline\Pipeline;

class Provider implements ListenerProviderInterface
{
    /**
     * @var array<class-string, callable[]>
     */
    protected array $listeners = [];

    /**
     * @param class-string $eventName
     */
    public function listen(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * @return iterable<callable(Event): Event>
     */
    public function getListenersForEvent(object $event): iterable
    {
        $image = new \ReflectionClass($event);
        do {
            $eventName = $image->getName();
            if (isset($this->listeners[$eventName])) {
                yield from $this->listeners[$eventName];
            }
        } while ($image = $image->getParentClass());
    }

    public function listenerPipeline(Event $event): Pipelayer
    {
        $pipeline = new Pipeline();
        $duplicates = [];
        /** @var callable(object): object $listener */
        foreach ($this->getListenersForEvent($event) as $listener) {
            if (in_array($listener, $duplicates, true)) {
                continue;
            }
            $duplicates[] = $listener;
            $pipeline
                ->pipe($listener)
                ->pipe(function (object $event) {
                    if ($event instanceof Event && !$event->isPropagationStopped()) {
                        return $event;
                    }
                });
        }
        return $pipeline;
    }
}

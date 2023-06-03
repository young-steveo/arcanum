<?php

declare(strict_types=1);

namespace Arcanum\Echo;

use Psr\EventDispatcher\ListenerProviderInterface;

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
}

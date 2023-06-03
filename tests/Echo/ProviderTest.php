<?php

declare(strict_types=1);

namespace Arcanum\Test\Echo;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Echo\Provider;
use Arcanum\Echo\Event;

#[CoversClass(Provider::class)]
final class ProviderTest extends TestCase
{
    public function testListen(): void
    {
        // Arrange
        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $listener = fn (Event $event): Event => $event;
        $provider = new Provider();

        // Act
        $provider->listen(eventName: get_class($event), listener: $listener);
        $result = $provider->getListenersForEvent($event);

        // Assert
        $count = 0;
        foreach ($result as $item) {
            $count++;
            $this->assertSame($listener, $item);
        }
        $this->assertSame(1, $count);
    }

    public function testGetEmptyListeners(): void
    {
        // Arrange
        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $provider = new Provider();

        // Act
        $result = $provider->getListenersForEvent($event);

        // Assert
        $count = 0;
        foreach ($result as $item) {
            $count++;
        }
        $this->assertSame(0, $count);
    }

    public function testGetListenersForEventIncludingListenersForParents(): void
    {
        // Arrange
        $event = new Fixture\ChildTriggered();
        $provider = new Provider();

        // Act
        // all three of these should be returned
        $provider->listen(Event::class, fn (Event $event): Event => $event);
        $provider->listen(Fixture\ChildTriggered::class, fn (Event $event): Event => $event);
        $provider->listen(Fixture\SomethingHappened::class, fn (Event $event): Event => $event);

        // add one that should not be returned
        $provider->listen(static::class, fn (Event $event): Event => $event);
        $result = $provider->getListenersForEvent($event);

        // Assert
        $count = 0;
        foreach ($result as $item) {
            $count++;
        }
        $this->assertSame(3, $count);
    }
}

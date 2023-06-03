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
        $this->assertCount(1, $result);
        foreach ($result as $item) {
            $this->assertSame($listener, $item);
        }
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
        $this->assertCount(0, $result);
    }
}

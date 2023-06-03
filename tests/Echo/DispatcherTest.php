<?php

declare(strict_types=1);

namespace Arcanum\Test\Echo;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Echo\Dispatcher;
use Arcanum\Echo\Provider;
use Arcanum\Echo\Event;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(Dispatcher::class)]
#[UsesClass(\Arcanum\Echo\UnknownEvent::class)]
final class DispatcherTest extends TestCase
{
    public function testDispatch(): void
    {
        // Arrange

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getListenersForEvent'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getListenersForEvent')
            ->willReturn([]);

        /** @var Event&\PHPUnit\Framework\MockObject\MockObject */
        $event = $this->getMockBuilder(\Arcanum\Echo\Event::class)
            ->onlyMethods(['stopPropagation', 'isPropagationStopped'])
            ->getMock();

        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $event
            ->expects($this->never())
            ->method('isPropagationStopped');

        $dispatcher = new \Arcanum\Echo\Dispatcher($provider);

        // Act
        $result = $dispatcher->dispatch($event);

        // Assert
        $this->assertSame($event, $result);
    }

    public function testDispatchUnknownObject(): void
    {
        // Arrange

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getListenersForEvent'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getListenersForEvent')
            ->willReturn([]);

        $event = new \stdClass();
        $dispatcher = new \Arcanum\Echo\Dispatcher($provider);

        // Act
        $result = $dispatcher->dispatch($event);

        // Assert
        $this->assertSame($event, $result);
    }

    public function testDispatchWithListeners(): void
    {
        // Arrange
        $count = 0;

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getListenersForEvent'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getListenersForEvent')
            ->willReturn([
                function (Event $event) use (&$count): Event {
                    $count++;
                    return $event;
                },
                function (Event $event) use (&$count): Event {
                    $count++;
                    return $event;
                },
                function (Event $event) use (&$count): Event {
                    $count++;
                    return $event;
                },
            ]);

        /** @var Event&\PHPUnit\Framework\MockObject\MockObject */
        $event = $this->getMockBuilder(\Arcanum\Echo\Event::class)
            ->onlyMethods(['stopPropagation', 'isPropagationStopped'])
            ->getMock();

        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $event
            ->expects($this->exactly(3))
            ->method('isPropagationStopped')
            ->willReturn(false);

        $dispatcher = new \Arcanum\Echo\Dispatcher($provider);

        // Act
        $result = $dispatcher->dispatch($event);

        // Assert
        $this->assertSame($event, $result);
        $this->assertSame(3, $count);
    }

    public function testDispatchWithListenersAndStopPropagation(): void
    {
        // Arrange
        $count = 0;
        $listener = function (Event $event) use (&$count): Event {
            $count++;
            return $event;
        };

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getListenersForEvent'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getListenersForEvent')
            ->willReturn([
                $listener,
                function (Event $event) use (&$count): Event {
                    $count++;
                    $event->stopPropagation();
                    return $event;
                },
                $listener, // should not be called
            ]);


        /** @var Event&\PHPUnit\Framework\MockObject\MockObject */
        $event = $this->getMockBuilder(\Arcanum\Echo\Event::class)
            ->onlyMethods(['stopPropagation', 'isPropagationStopped'])
            ->getMock();

        $event
            ->expects($this->once())
            ->method('stopPropagation');

        $event
            ->expects($this->exactly(3))
            ->method('isPropagationStopped')
            ->willReturnOnConsecutiveCalls(false, false, true);

        $dispatcher = new \Arcanum\Echo\Dispatcher($provider);

        // Act
        $result = $dispatcher->dispatch($event);

        // Assert
        $this->assertSame($event, $result);
        $this->assertSame(2, $count);
    }

    public function testIfSameListenerIsRegisteredForTwoEventsInInheritanceChainItIsOnlyDispatchedToOnce(): void
    {
        // Arrange
        $count = 0;
        $listener = function (Event $event) use (&$count): Event {
            $count++;
            return $event;
        };

        /** @var Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(Provider::class)
            ->onlyMethods(['getListenersForEvent'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getListenersForEvent')
            ->willReturn([
                $listener,
                $listener,
            ]);

        /** @var Event&\PHPUnit\Framework\MockObject\MockObject */
        $event = $this->getMockBuilder(\Arcanum\Echo\Event::class)
            ->onlyMethods(['stopPropagation', 'isPropagationStopped'])
            ->getMock();

        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $event
            ->expects($this->exactly(2))
            ->method('isPropagationStopped')
            ->willReturn(false);

        $dispatcher = new \Arcanum\Echo\Dispatcher($provider);

        // Act
        $result = $dispatcher->dispatch($event);

        // Assert
        $this->assertSame($event, $result);
        $this->assertSame(1, $count);
    }
}

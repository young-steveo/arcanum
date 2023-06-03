<?php

declare(strict_types=1);

namespace Arcanum\Test\Echo;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Echo\Event::class)]
final class EventTest extends TestCase
{
    public function testStopPropagation(): void
    {
        // Arrange

        /** @var \Arcanum\Echo\Event&\PHPUnit\Framework\MockObject\MockObject */
        $event = $this->getMockBuilder(\Arcanum\Echo\Event::class)
            ->onlyMethods([])
            ->getMock();

        // Act
        $event->stopPropagation();

        // Assert
        $this->assertTrue($event->isPropagationStopped());
    }
}

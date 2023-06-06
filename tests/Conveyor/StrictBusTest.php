<?php

declare(strict_types=1);

namespace Arcanum\Test\Conveyor;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Conveyor\StrictBus;
use Arcanum\Conveyor\SwiftBus;
use Arcanum\Cabinet\Container;
use Arcanum\Test\Fixture\Command\DoSomething;
use Arcanum\Test\Fixture\Command\DoSomethingHandler;
use Arcanum\Test\Fixture\Command\DoSomethingResult;

#[CoversClass(StrictBus::class)]
#[UsesClass(SwiftBus::class)]
final class StrictBusTest extends TestCase
{
    public function testDispatchHappyPath(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $container->expects($this->once())
            ->method('get')
            ->with(DoSomethingHandler::class)
            ->willReturn(new DoSomethingHandler());

        $bus = new StrictBus($container);
        $command = new DoSomething('test');

        // Act
        $result = $bus->dispatch($command);

        // Assert
        $this->assertInstanceOf(DoSomethingResult::class, $result);
        $this->assertSame('test', $result->name);
    }
}

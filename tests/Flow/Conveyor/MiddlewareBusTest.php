<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Conveyor;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\Conveyor\MiddlewareBus;
use Arcanum\Cabinet\Container;
use Arcanum\Test\Flow\Conveyor\Fixture\DoSomething;
use Arcanum\Test\Flow\Conveyor\Fixture\DoSomethingHandler;
use Arcanum\Test\Flow\Conveyor\Fixture\DoSomethingResult;
use Arcanum\Flow\Continuum\Continuum;

#[CoversClass(MiddlewareBus::class)]
#[UsesClass(\Arcanum\Flow\Pipeline\Pipeline::class)]
#[UsesClass(\Arcanum\Flow\Pipeline\StandardProcessor::class)]
final class MiddlewareBusTest extends TestCase
{
    public function testDispatchHappyPath(): void
    {
        // Arrange
        $command = new DoSomething('test');
        $response = new DoSomethingResult('test');

        /** @var Continuum&\PHPUnit\Framework\MockObject\MockObject */
        $dispatchFlow = $this->getMockBuilder(Continuum::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__invoke'])
            ->getMock();

        $dispatchFlow->expects($this->once())
            ->method('__invoke')
            ->with($command)
            ->willReturn($command);

        /** @var Continuum&\PHPUnit\Framework\MockObject\MockObject */
        $responseFlow = $this->getMockBuilder(Continuum::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__invoke'])
            ->getMock();

        $responseFlow->expects($this->once())
            ->method('__invoke')
            ->with($response)
            ->willReturn($response);


        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $container->expects($this->once())
            ->method('get')
            ->with(DoSomethingHandler::class)
            ->willReturn(new DoSomethingHandler());

        $bus = new MiddlewareBus($container, $dispatchFlow, $responseFlow);

        // Act
        $result = $bus->dispatch($command);

        // Assert
        $this->assertInstanceOf(DoSomethingResult::class, $result);
        $this->assertSame('test', $result->name);
    }
}

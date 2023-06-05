<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Flow\Pipeline;
use Arcanum\Flow\Processor;

#[CoversClass(Pipeline::class)]
final class PipelineTest extends TestCase
{
    public function testPipeline(): void
    {
        // Arrange
        $stage = fn(object $payload): object => $payload;
        $payload = new \stdClass();

        /** @var Processor&\PHPUnit\Framework\MockObject\MockObject */
        $processor = $this->getMockBuilder(Processor::class)
            ->onlyMethods(['process'])
            ->getMock();

        $processor->expects($this->once())
            ->method('process')
            ->with($payload, $stage, $stage)
            ->willReturn($payload);


        $pipeline = new Pipeline($processor);

        // Act
        $result = $pipeline->pipe($stage)->pipe($stage)->send($payload);

        // Assert
        $this->assertSame($payload, $result);
    }

    public function testInvokePipeline(): void
    {
        // Arrange
        $stage = fn(object $payload): object => $payload;
        $payload = new \stdClass();

        /** @var Processor&\PHPUnit\Framework\MockObject\MockObject */
        $processor = $this->getMockBuilder(Processor::class)
            ->onlyMethods(['process'])
            ->getMock();

        $processor->expects($this->once())
            ->method('process')
            ->with($payload, $stage, $stage)
            ->willReturn($payload);


        $pipeline = new Pipeline($processor);

        // Act
        $pipeline = $pipeline->pipe($stage)->pipe($stage);

        $result = $pipeline($payload);

        // Assert
        $this->assertSame($payload, $result);
    }
}

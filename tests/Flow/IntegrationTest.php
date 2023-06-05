<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\Pipeline;
use Arcanum\Flow\StandardProcessor;

#[CoversClass(Pipeline::class)]
#[UsesClass(StandardProcessor::class)]
final class IntegrationTest extends TestCase
{
    public function testPipelineAsStage(): void
    {
        // Arrange
        $stage = fn(object $payload): object => $payload;
        $payload = new \stdClass();
        $parentProcessor = new StandardProcessor();
        $stageProcessor = new StandardProcessor();
        $pipeline = new Pipeline($parentProcessor);
        $stagePipeline = new Pipeline($stageProcessor);

        // Act
        $stagePipeline = $stagePipeline
            ->pipe($stage)
            ->pipe($stage);

        $result = $pipeline
            ->pipe($stage)
            ->pipe($stagePipeline)
            ->pipe($stage)
            ->send($payload);

        // Assert
        $this->assertSame($payload, $result);
    }
}

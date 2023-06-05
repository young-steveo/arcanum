<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Pipeline;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\Pipeline\Pipeline;
use Arcanum\Flow\Continuum\Continuum;
use Arcanum\Flow\Pipeline\StandardProcessor;
use Arcanum\Flow\Continuum\StandardAdvancer;
use Arcanum\Test\Flow\Continuum\Fixture\BasicProgression;

#[CoversClass(Pipeline::class)]
#[UsesClass(StandardProcessor::class)]
#[UsesClass(Continuum::class)]
#[UsesClass(StandardAdvancer::class)]
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

    public function testContinuumAsStage(): void
    {
        // Arrange
        $service = (object)['middleware' => 0, 'stage' => 0];
        $stage = function (object $payload) use ($service): object {
            if ($payload === $service) {
                $payload->stage++;
            }
            return $payload;
        };
        $processor = new StandardProcessor();
        $pipeline = new Pipeline($processor);

        $advancer = new StandardAdvancer();
        $middleware = new Continuum($advancer);

        // Act
        $middleware = $middleware
            ->add(BasicProgression::fromClosure(function (object $payload, callable $next) use ($service): void {
                if ($payload === $service) {
                    $payload->middleware++;
                }
                $next();
            }))
            ->add(BasicProgression::fromClosure(function (object $payload, callable $next) use ($service): void {
                if ($payload === $service) {
                    $payload->middleware++;
                }
                $next();
            }));

        $result = $pipeline
            ->pipe($stage)
            ->pipe($middleware)
            ->pipe($stage)
            ->send($service);

        // Assert
        $this->assertSame($service, $result);
        $this->assertSame(2, $service->stage);
        $this->assertSame(2, $service->middleware);
    }
}

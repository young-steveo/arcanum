<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Continuum;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Flow\Continuum\Continuum;
use Arcanum\Flow\Continuum\Advancer;
use Arcanum\Test\Flow\Continuum\Fixture\BasicProgression;

#[CoversClass(Continuum::class)]
final class ContinuumTest extends TestCase
{
    public function testContinuum(): void
    {
        // Arrange
        $stage = BasicProgression::fromClosure(fn(object $payload, callable $next): object => $next());
        $payload = new \stdClass();

        /** @var Advancer&\PHPUnit\Framework\MockObject\MockObject */
        $Advancer = $this->getMockBuilder(Advancer::class)
            ->onlyMethods(['advance'])
            ->getMock();

        $Advancer->expects($this->once())
            ->method('advance')
            ->with($payload, $stage, $stage)
            ->willReturn($payload);


        $continuum = new Continuum($Advancer);

        // Act
        $result = $continuum->add($stage)->add($stage)->send($payload);

        // Assert
        $this->assertSame($payload, $result);
    }

    public function testInvokeContinuum(): void
    {
        // Arrange
        $stage = BasicProgression::fromClosure(fn(object $payload, callable $next): object => $next());
        $payload = new \stdClass();

        /** @var Advancer&\PHPUnit\Framework\MockObject\MockObject */
        $Advancer = $this->getMockBuilder(Advancer::class)
            ->onlyMethods(['advance'])
            ->getMock();

        $Advancer->expects($this->once())
            ->method('advance')
            ->with($payload, $stage, $stage)
            ->willReturn($payload);


        $continuum = new Continuum($Advancer);

        // Act
        $continuum = $continuum->add($stage)->add($stage);

        $result = $continuum($payload);

        // Assert
        $this->assertSame($payload, $result);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use Arcanum\Cabinet\MiddlewareProgression;
use Arcanum\Test\Flow\Continuum\Fixture\BasicProgression;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MiddlewareProgression::class)]
final class MiddlewareProgressionTest extends TestCase
{
    public function testMiddlewareProgression(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\ClassResolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\ClassResolver::class)
            ->getMock();

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(BasicProgression::class)
            ->willReturn(new BasicProgression(function (object $payload, callable $next) {
                $next();
            }));

        $middleware = new MiddlewareProgression(BasicProgression::class, $resolver);
        $payload = new \stdClass();
        $called = false;
        $next = function () use (&$called) {
            $called = true;
        };

        // Act
        $middleware($payload, $next);

        // Assert
        $this->assertTrue($called);
    }
}

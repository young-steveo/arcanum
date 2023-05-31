<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Cabinet\SimpleProvider::class)]
final class SimpleProviderTest extends TestCase
{
    public function testSimnpleProvider(): void
    {
        // Arrange
        $factory = fn() => 'bar';
        $provider = \Arcanum\Cabinet\SimpleProvider::fromFactory($factory);
        $container = new \Arcanum\Cabinet\Container();

        // Act
        $result = $provider($container);

        // Assert
        $this->assertEquals('bar', $result);
    }
}

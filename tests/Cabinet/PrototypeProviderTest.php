<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Cabinet\PrototypeProvider::class)]
final class PrototypeProviderTest extends TestCase
{
    public function testSimnpleProvider(): void
    {
        // Arrange
        $provider = \Arcanum\Cabinet\PrototypeProvider::fromFactory(fn() => new \stdClass());
        $container = $this->getMockBuilder(\Arcanum\Cabinet\Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Act
        $result = $provider($container);

        // Assert
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertNotSame($provider($container), $result);
    }
}

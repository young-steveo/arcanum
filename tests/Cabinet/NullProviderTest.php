<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Cabinet\NullProvider::class)]
final class NullProviderTest extends TestCase
{
    public function testSimnpleProvider(): void
    {
        // Arrange
        $provider = new \Arcanum\Cabinet\NullProvider();
        $container = $this->getMockBuilder(\Arcanum\Cabinet\Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Act
        $result = $provider($container);

        // Assert
        $this->assertNull($result);
    }
}

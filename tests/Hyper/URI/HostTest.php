<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\URI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Hyper\URI\Host;

#[CoversClass(Host::class)]
final class HostTest extends TestCase
{
    public function testHost(): void
    {
        // Arrange
        $scheme = new Host('example.com');

        // Act
        $data = (string)$scheme;

        // Assert
        $this->assertSame('example.com', $data);
    }

    public function testIsEmpty(): void
    {
        // Arrange
        $scheme = new Host('');
        $scheme2 = new Host('example.com');

        // Act
        $isEmpty = $scheme->isEmpty();
        $isNotEmpty = $scheme2->isEmpty();

        // Assert
        $this->assertTrue($isEmpty);
        $this->assertFalse($isNotEmpty);
    }

    public function testStaticLocalhost(): void
    {
        // Arrange
        $scheme = Host::localhost();

        // Act
        $data = (string)$scheme;

        // Assert
        $this->assertSame('localhost', $data);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\URI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Hyper\URI\Scheme;

#[CoversClass(Scheme::class)]
final class SchemeTest extends TestCase
{
    public function testScheme(): void
    {
        // Arrange
        $scheme = new Scheme('https');

        // Act
        $data = (string)$scheme;

        // Assert
        $this->assertSame('https', $data);
    }

    public function testIsWebScheme(): void
    {
        // Arrange
        $scheme = new Scheme('https');
        $junk = new Scheme('junk');

        // Act
        $isWebScheme = $scheme->isWebScheme();
        $isNotWebScheme = $junk->isWebScheme();

        // Assert
        $this->assertTrue($isWebScheme);
        $this->assertFalse($isNotWebScheme);
    }

    public function testIsEmpty(): void
    {
        // Arrange
        $scheme = new Scheme('');
        $scheme2 = new Scheme('https');

        // Act
        $isEmpty = $scheme->isEmpty();
        $isNotEmpty = $scheme2->isEmpty();

        // Assert
        $this->assertTrue($isEmpty);
        $this->assertFalse($isNotEmpty);
    }
}

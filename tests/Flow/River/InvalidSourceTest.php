<?php

declare(strict_types=1);

namespace Arcanum\Flow\River;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Flow\River\InvalidSource::class)]
final class InvalidSourceTest extends TestCase
{
    public function testInvalidSource(): void
    {
        // Arrange
        $invalidKey = new \Arcanum\Flow\River\InvalidSource('foo');

        // Act
        $message = $invalidKey->getMessage();

        // Assert
        $this->assertEquals('Invalid Source: foo', $message);
    }
}

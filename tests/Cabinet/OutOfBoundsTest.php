<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Cabinet\OutOfBounds::class)]
final class OutOfBoundsTest extends TestCase
{
    public function testOutOfBounds(): void
    {
        // Arrange
        $outOfBounds = new \Arcanum\Cabinet\OutOfBounds('foo');

        // Act
        $message = $outOfBounds->getMessage();

        // Assert
        $this->assertEquals('Out of Bounds: foo', $message);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Error;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Cabinet\Error\OutOfBounds::class)]
final class OutOfBoundsTest extends TestCase
{
    public function testOutOfBounds(): void
    {
        // Arrange
        $outOfBounds = new \Arcanum\Cabinet\Error\OutOfBounds('foo');

        // Act
        $message = $outOfBounds->getMessage();

        // Assert
        $this->assertEquals('Out of Bounds: foo', $message);
    }
}

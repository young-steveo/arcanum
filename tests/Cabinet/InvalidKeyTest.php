<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Cabinet\InvalidKey::class)]
final class InvalidKeyTest extends TestCase
{
    public function testInvalidKey(): void
    {
        // Arrange
        $invalidKey = new \Arcanum\Cabinet\InvalidKey('foo');

        // Act
        $message = $invalidKey->getMessage();

        // Assert
        $this->assertEquals('Invalid Key: foo', $message);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Error;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Cabinet\Error\InvalidKey::class)]
final class InvalidKeyTest extends TestCase
{
    public function testInvalidKey(): void
    {
        // Arrange
        $invalidKey = new \Arcanum\Cabinet\Error\InvalidKey('foo');

        // Act
        $message = $invalidKey->getMessage();

        // Assert
        $this->assertEquals('Invalid Key: foo', $message);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Error;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Codex\Error\UnknownClass::class)]
final class UnknownClassTest extends TestCase
{
    public function testUnknownClass(): void
    {
        // Arrange
        $unknownClass = new \Arcanum\Codex\Error\UnknownClass(
            message: 'foo'
        );

        // Act
        $message = $unknownClass->getMessage();

        // Assert
        $this->assertSame('Unknown Class: foo', $message);
    }
}

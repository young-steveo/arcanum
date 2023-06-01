<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Error;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Cabinet\Error\UnknownClass::class)]
final class UnknownClassTest extends TestCase
{
    public function testUnknownClass(): void
    {
        // Arrange
        $unknownClass = new \Arcanum\Cabinet\Error\UnknownClass(
            message: 'foo'
        );

        // Act
        $message = $unknownClass->getMessage();

        // Assert
        $this->assertSame('Unknown Class: foo', $message);
    }
}

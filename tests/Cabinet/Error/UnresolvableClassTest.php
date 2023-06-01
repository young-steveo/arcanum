<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Error;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(\Arcanum\Cabinet\Error\UnresolvableClass::class)]
final class UnresolvableClassTest extends TestCase
{
    public function testUnresolvableClass(): void
    {
        // Arrange
        $UnresolvableClass = new \Arcanum\Cabinet\Error\UnresolvableClass(
            message: 'foo'
        );

        // Act
        $message = $UnresolvableClass->getMessage();

        // Assert
        $this->assertSame('Unresolvable Class: foo', $message);
    }
}

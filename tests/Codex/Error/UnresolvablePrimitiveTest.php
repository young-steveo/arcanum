<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Error;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(\Arcanum\Codex\Error\UnresolvablePrimitive::class)]
final class UnresolvablePrimitiveTest extends TestCase
{
    public function testUnresolvablePrimitive(): void
    {
        // Arrange
        $UnresolvablePrimitive = new \Arcanum\Codex\Error\UnresolvablePrimitive(
            message: 'string'
        );

        // Act
        $message = $UnresolvablePrimitive->getMessage();

        // Assert
        $this->assertSame('Unresolvable Primitive: string', $message);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Error;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(\Arcanum\Codex\Error\UnresolvableUnionType::class)]
final class UnresolvableUnionTypeTest extends TestCase
{
    public function testUnresolvableUnionType(): void
    {
        // Arrange
        $UnresolvableUnionType = new \Arcanum\Codex\Error\UnresolvableUnionType(
            message: 'string'
        );

        // Act
        $message = $UnresolvableUnionType->getMessage();

        // Assert
        $this->assertSame('Unresolvable Union Type: string', $message);
    }
}

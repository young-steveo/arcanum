<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Flow\Interrupted::class)]
final class InterruptedTest extends TestCase
{
    public function testInterrupted(): void
    {
        // Arrange
        $interrupted = new \Arcanum\Flow\Interrupted(
            message: 'foo'
        );

        // Act
        $message = $interrupted->getMessage();

        // Assert
        $this->assertSame('Flow Interrupted: foo', $message);
    }
}

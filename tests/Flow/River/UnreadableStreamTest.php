<?php

declare(strict_types=1);

namespace Arcanum\Flow\River;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Flow\River\UnreadableStream::class)]
final class UnreadableStreamTest extends TestCase
{
    public function testUnreadableStream(): void
    {
        // Arrange
        $invalidKey = new \Arcanum\Flow\River\UnreadableStream('foo');

        // Act
        $message = $invalidKey->getMessage();

        // Assert
        $this->assertEquals('Unreadable stream: foo', $message);
    }
}

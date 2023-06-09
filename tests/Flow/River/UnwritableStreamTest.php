<?php

declare(strict_types=1);

namespace Arcanum\Flow\River;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Flow\River\UnwritableStream::class)]
final class UnwritableStreamTest extends TestCase
{
    public function testUnwritableStream(): void
    {
        // Arrange
        $invalidKey = new \Arcanum\Flow\River\UnwritableStream('foo');

        // Act
        $message = $invalidKey->getMessage();

        // Assert
        $this->assertEquals('Unwritable stream: foo', $message);
    }
}

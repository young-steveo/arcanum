<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\River;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Flow\River\UnseekableStream::class)]
final class UnseekableStreamTest extends TestCase
{
    public function testUnseekableStream(): void
    {
        // Arrange
        $invalidKey = new \Arcanum\Flow\River\UnseekableStream('foo');

        // Act
        $message = $invalidKey->getMessage();

        // Assert
        $this->assertEquals('Unseekable stream: foo', $message);
    }
}

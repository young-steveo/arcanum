<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\Files;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Hyper\Files\InvalidFile::class)]
final class InvalidFileTest extends TestCase
{
    public function testInvalidSource(): void
    {
        // Arrange
        $invalidKey = new \Arcanum\Hyper\Files\InvalidFile('foo');

        // Act
        $message = $invalidKey->getMessage();

        // Assert
        $this->assertEquals('Invalid File: foo', $message);
    }
}

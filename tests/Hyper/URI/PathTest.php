<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\URI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Hyper\URI\Path;

#[CoversClass(Path::class)]
final class PathTest extends TestCase
{
    public function testPath(): void
    {
        // Arrange
        $path = new Path('/path/to/resource');

        // Act
        $data = (string)$path;

        // Assert
        $this->assertSame('/path/to/resource', $data);
    }

    public function testPathEncodesNonURLCharacters(): void
    {
        // Arrange
        $path = new Path('/p@th/to:/r%source with spaces');

        // Act
        $data = (string)$path;

        // Assert
        $this->assertSame('/p@th/to:/r%25source%20with%20spaces', $data);
    }

    public function testPathContains(): void
    {
        // Arrange
        $path = new Path('/path/to/resource');

        // Act
        $data = $path->contains('/path/to');
        $nope = $path->contains('/path/too');

        // Assert
        $this->assertTrue($data);
        $this->assertFalse($nope);
    }

    public function testPathStartsWith(): void
    {
        // Arrange
        $path = new Path('/path/to/resource');

        // Act
        $data = $path->startsWith('/path/to');
        $nope = $path->startsWith('/path/too');

        // Assert
        $this->assertTrue($data);
        $this->assertFalse($nope);
    }
}

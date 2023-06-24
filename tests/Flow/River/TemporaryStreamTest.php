<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\River;

use Arcanum\Flow\River\TemporaryStream;
use Arcanum\Flow\River\Stream;
use Arcanum\Flow\River\StreamResource;
use Arcanum\Flow\River\InvalidSource;
use Arcanum\Gather\Registry;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(TemporaryStream::class)]
#[UsesClass(Stream::class)]
#[UsesClass(StreamResource::class)]
#[UsesClass(Registry::class)]
#[UsesClass(InvalidSource::class)]
final class TemporaryStreamTest extends TestCase
{
    public function testGetNew(): void
    {
        // Arrange
        $temp = TemporaryStream::getNew();

        // Act
        $temp->write('foo');
        $temp->rewind();

        // Assert
        $this->assertSame('foo', $temp->getContents());
    }

    public function testConstructorThrowsInvalidSourceIfResourceIsNotTemporary(): void
    {
        // Arrange
        $resource = fopen('php://memory', 'r');
        if ($resource === false) {
            $this->fail('Could not open temporary stream');
        }

        // Assert
        $this->expectException(InvalidSource::class);
        $this->expectExceptionMessage('TemporaryStream can only be constructed from a temporary stream');

        // Act
        new TemporaryStream(StreamResource::wrap($resource));
    }
}

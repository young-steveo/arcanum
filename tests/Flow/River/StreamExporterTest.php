<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\River;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\River\StreamExporter;
use Arcanum\Flow\River\StreamResource;
use Arcanum\Flow\River\InvalidSource;
use Arcanum\Flow\River\UnreadableStream;

#[CoversClass(StreamExporter::class)]
#[UsesClass(InvalidSource::class)]
#[UsesClass(UnreadableStream::class)]
final class StreamExporterTest extends TestCase
{
    public function testInvalidStreamResource(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource'])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(false);

        // Assert
        $this->expectException(InvalidSource::class);

        // Act
        new StreamExporter($resource);
    }

    public function testGetContents(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetContents'])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetContents')
            ->willReturn('Hello World');

        $streamExporter = new StreamExporter($resource);

        // Act
        $contents = $streamExporter->getContents();

        // Assert
        $this->assertSame('Hello World', $contents);
    }

    public function testStreamThrowsException(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetContents'])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetContents')
            ->willThrowException(new \Exception('Stream error'));

        $streamExporter = new StreamExporter($resource);

        // Assert
        $this->expectException(UnreadableStream::class);
        $this->expectExceptionMessage('Unreadable stream: Could not read stream');

        // Act
        $streamExporter->getContents();
    }
}

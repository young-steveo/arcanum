<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\River;

use Arcanum\Flow\River\Bank;
use Arcanum\Flow\River\Copyable;
use Psr\Http\Message\StreamInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Bank::class)]
final class BankTest extends TestCase
{
    public function testCopyTo(): void
    {
        // Arrange
        /** @var StreamInterface&\PHPUnit\Framework\MockObject\MockObject */
        $source = $this->getMockBuilder(StreamInterface::class)
            ->getMock();

        /** @var StreamInterface&\PHPUnit\Framework\MockObject\MockObject */
        $target = $this->getMockBuilder(StreamInterface::class)
            ->getMock();

        $source->expects($this->once())
            ->method('eof')
            ->willReturn(true);

        $source->expects($this->never())
            ->method('read');

        $target->expects($this->never())
            ->method('write');

        // Act
        Bank::copyTo($source, $target);
    }

    public function testCopyToWithData(): void
    {
        // Arrange
        /** @var StreamInterface&\PHPUnit\Framework\MockObject\MockObject */
        $source = $this->getMockBuilder(StreamInterface::class)
            ->getMock();

        /** @var StreamInterface&\PHPUnit\Framework\MockObject\MockObject */
        $target = $this->getMockBuilder(StreamInterface::class)
            ->getMock();

        $source->expects($this->exactly(2))
            ->method('eof')
            ->willReturnOnConsecutiveCalls(false, true);

        $source->expects($this->once())
            ->method('read')
            ->with(4096)
            ->willReturn('foo');

        $target->expects($this->once())
            ->method('write')
            ->with('foo')
            ->willReturn(3);

        // Act
        Bank::copyTo($source, $target);
    }

    public function testCopyToSoourceIsCopyableInterface(): void
    {
        // Arrange
        /** @var Copyable&\PHPUnit\Framework\MockObject\MockObject */
        $source = $this->getMockBuilder(Copyable::class)
            ->getMock();

        /** @var StreamInterface&\PHPUnit\Framework\MockObject\MockObject */
        $target = $this->getMockBuilder(StreamInterface::class)
            ->getMock();

        $source->expects($this->never())
            ->method('eof');

        $source->expects($this->never())
            ->method('read');

        $target->expects($this->never())
            ->method('write');

        $source->expects($this->once())
            ->method('copyTo')
            ->with($target);

        // Act
        Bank::copyTo($source, $target);
    }

    public function testDeleteIfMoved(): void
    {
        // Arrange
        $filename = \tempnam(\sys_get_temp_dir(), 'foo');

        if ($filename === false) {
            $this->fail('Could not create temporary file');
        }

        /** @var StreamInterface&\PHPUnit\Framework\MockObject\MockObject */
        $stream = $this->getMockBuilder(StreamInterface::class)
            ->getMock();

        $stream->expects($this->once())
            ->method('getMetadata')
            ->willReturn([
                'uri' => $filename,
            ]);

        // Act
        Bank::deleteIfMoved($stream, \sys_get_temp_dir() . 'target');

        // Assert
        $this->assertFileDoesNotExist($filename);
    }

    public function testDeleteIfMovedDoesNotDeleteIfTagetMatchesFile(): void
    {
        // Arrange
        $filename = \tempnam(\sys_get_temp_dir(), 'foo');

        if ($filename === false) {
            $this->fail('Could not create temporary file');
        }

        /** @var StreamInterface&\PHPUnit\Framework\MockObject\MockObject */
        $stream = $this->getMockBuilder(StreamInterface::class)
            ->getMock();

        $stream->expects($this->once())
            ->method('getMetadata')
            ->willReturn([
                'uri' => $filename,
            ]);

        // Act
        Bank::deleteIfMoved($stream, $filename);

        // Assert
        $this->assertFileExists($filename);

        // Cleanup
        \unlink($filename);
    }
}

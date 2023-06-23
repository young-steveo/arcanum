<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\River;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\River\Stream;
use Arcanum\Flow\River\StreamResource;
use Arcanum\Flow\River\InvalidSource;
use Arcanum\Flow\River\DetachedSource;
use Arcanum\Flow\River\UnreadableStream;
use Arcanum\Flow\River\UnseekableStream;
use Arcanum\Flow\River\UnwritableStream;
use Arcanum\Gather\Registry;

#[CoversClass(Stream::class)]
#[UsesClass(InvalidSource::class)]
#[UsesClass(DetachedSource::class)]
#[UsesClass(UnreadableStream::class)]
#[UsesClass(UnseekableStream::class)]
#[UsesClass(UnwritableStream::class)]
#[UsesClass(Registry::class)]
final class StreamTest extends TestCase
{
    public function testNewStream(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetMetaData', 'fclose', 'export'])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_data' => [
                    'HTTP/1.1 200 OK',
                    'Age: 244629',
                    'Cache-Control: max-age=604800',
                    'Content-Type: text/html; charset=UTF-8',
                    'Date: Sat, 20 Nov 2021 18:17:57 GMT',
                    'Etag: "3147526947+ident"',
                    'Expires: Sat, 27 Nov 2021 18:17:57 GMT',
                    'Last-Modified: Thu, 17 Oct 2019 07:18:26 GMT',
                    'Server: ECS (chb/0286)',
                    'Vary: Accept-Encoding',
                    'X-Cache: HIT',
                    'Content-Length: 1256',
                    'Connection: close',
                ],
                'wrapper_type' => 'http',
                'stream_type' => 'tcp_socket/ssl',
                'mode' => 'r',
                'unread_bytes' => 1256,
                'seekable' => false,
                'uri' => 'http://www.example.com/',
            ]);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));


        // Act
        $stream = new Stream($resource);


        // Assert
        $this->assertInstanceOf(Stream::class, $stream);
    }

    public function testNewStreamFailsIfResourceIsNotLive(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetMetaData', 'fclose', 'export'])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(false);

        $resource->expects($this->never())
            ->method('streamGetMetaData');

        $resource->expects($this->never())
            ->method('fclose');

        $resource->expects($this->never())
            ->method('export');

        // Assert
        $this->expectException(InvalidSource::class);

        // Act
        new Stream($resource);
    }

    public function testDetach(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetMetaData', 'fclose', 'export'])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('fclose');

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));


        // Act
        $stream = new Stream($resource);

        $result = $stream->detach();
        $again = $stream->detach();

        $this->assertTrue(is_resource($result));
        $this->assertFalse(is_resource($again));
        $this->assertNull($stream->detach());
    }

    public function testIsReadable(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetMetaData', 'fclose', 'export'])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $result = $stream->isReadable();

        // Assert
        $this->assertTrue($result);
    }

    public function testIsWritable(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetMetaData', 'fclose', 'export'])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $result = $stream->isWritable();

        // Assert
        $this->assertTrue($result);
    }

    public function testIsSeekable(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetMetaData', 'fclose', 'export'])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $result = $stream->isSeekable();

        // Assert
        $this->assertTrue($result);
    }

    public function testEOF(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetMetaData', 'fclose', 'export', 'feof'])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('feof')
            ->willReturn(false);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $result = $stream->eof();

        // Assert
        $this->assertFalse($result);
    }

    public function testEOFThrowsDetechedSourceIfStreamIsDetached(): void
    {
        // Arrange
        $this->expectException(DetachedSource::class);
        $this->expectExceptionMessage('Detached source: Cannot operate on a detached stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetMetaData', 'fclose', 'export', 'feof'])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('feof');

        $resource->expects($this->never())
            ->method('fclose');

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->detach();
        $stream->eof();
    }

    public function testSeekAndTell(): void
    {
        // Arrange
        $expected = 10;

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'ftell',
                'fseek',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fseek')
            ->with($expected)
            ->willReturn(0);

        $resource->expects($this->once())
            ->method('ftell')
            ->willReturn($expected);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->seek(10);
        $result = $stream->tell();

        // Assert
        $this->assertSame($expected, $result);
    }

    public function testTellThrowsDetachedSourceIfStreamIsDetached(): void
    {
        // Arrange
        $this->expectException(DetachedSource::class);
        $this->expectExceptionMessage('Detached source: Cannot operate on a detached stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'ftell',
                'fseek',
            ])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('fseek');

        $resource->expects($this->never())
            ->method('ftell');

        $resource->expects($this->never())
            ->method('fclose');

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->detach();
        $stream->tell();
    }

    public function testTellThrowsUnreadableStreamIfResourceFTellReturnsFalse(): void
    {
        // Arrange
        $this->expectException(UnreadableStream::class);
        $this->expectExceptionMessage('Unable to determine stream position');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'ftell',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('ftell')
            ->willReturn(false);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->tell();
    }

    public function testSeekThrowsDetachedSourceIfStreamIsDetached(): void
    {
        // Arrange
        $this->expectException(DetachedSource::class);
        $this->expectExceptionMessage('Detached source: Cannot operate on a detached stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fseek',
            ])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('fseek');

        $resource->expects($this->never())
            ->method('fclose');

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->detach();
        $stream->seek(10);
    }

    public function testSeekThrowsUnseekableStreamIfStreamIsNotSeekable(): void
    {
        // Arrange
        $this->expectException(UnseekableStream::class);
        $this->expectExceptionMessage('Cannot seek a non-seekable stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fseek',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'r',
                'unread_bytes' => 0,
                'seekable' => false,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('fseek');

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r'));

        $stream = new Stream($resource);

        // Act
        $stream->seek(10);
    }

    public function testSeekThrowsUnseekableStreamIfResourceFSeekFails(): void
    {
        // Arrange
        $this->expectException(UnseekableStream::class);
        $this->expectExceptionMessage('Unable to seek to stream position 10 with whence 0');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fseek',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fseek')
            ->with(10, SEEK_SET)
            ->willReturn(-1);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->seek(10);
    }

    public function testRewind(): void
    {
        // Arrange
        $expected = 0;

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fseek'
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fseek')
            ->with(0)
            ->willReturn(0);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->rewind();
    }

    public function testGetMetadata(): void
    {
        // Arrange
        $expected = [
            'timed_out' => false,
            'blocked' => true,
            'eof' => false,
            'wrapper_type' => 'PHP',
            'stream_type' => 'MEMORY',
            'mode' => 'w+b',
            'unread_bytes' => 0,
            'seekable' => true,
            'uri' => 'php://memory',
        ];

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->exactly(2))
            ->method('streamGetMetaData')
            ->willReturn($expected);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $actual = $stream->getMetadata();

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function testGetMetaDataByKey(): void
    {
        // Arrange
        $expected = [
            'timed_out' => false,
            'blocked' => true,
            'eof' => false,
            'wrapper_type' => 'PHP',
            'stream_type' => 'MEMORY',
            'mode' => 'w+b',
            'unread_bytes' => 0,
            'seekable' => true,
            'uri' => 'php://memory',
        ];

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->exactly(2))
            ->method('streamGetMetaData')
            ->willReturn($expected);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $actual = $stream->getMetadata('stream_type');

        // Assert
        $this->assertSame($expected['stream_type'], $actual);
    }

    public function testGetMetadataReturnsNullIfKeyDoesNotExist(): void
    {
        // Arrange
        $expected = [
            'timed_out' => false,
            'blocked' => true,
            'eof' => false,
            'wrapper_type' => 'PHP',
            'stream_type' => 'MEMORY',
            'mode' => 'w+b',
            'unread_bytes' => 0,
            'seekable' => true,
            'uri' => 'php://memory',
        ];

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->exactly(2))
            ->method('streamGetMetaData')
            ->willReturn($expected);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $actual = $stream->getMetadata('foo_bar'); /** @phpstan-ignore-line */

        // Assert
        $this->assertNull($actual);
    }

    public function testGetMetadataThrowsDetachedSourceIfResourceIsDetached(): void
    {
        // Arrange
        $this->expectException(DetachedSource::class);
        $this->expectExceptionMessage('Detached source: Cannot operate on a detached stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
            ])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('fclose');

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->detach();
        $stream->getMetadata();
    }

    public function testGetContents(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'streamGetContents',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('streamGetContents')
            ->willReturn('foo');

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $result = $stream->getContents();

        // Assert
        $this->assertSame('foo', $result);
    }

    public function testGetContentsThrowsDetachedSourceIfStreamIsDetached(): void
    {
        // Arrange
        $this->expectException(DetachedSource::class);
        $this->expectExceptionMessage('Detached source: Cannot operate on a detached stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'streamGetContents',
            ])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('streamGetContents');

        $resource->expects($this->never())
            ->method('fclose');

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->detach();
        $stream->getContents();
    }

    public function testGetContentsThrowsUnreadableStreamIfStreamIsUnreadable(): void
    {
        // Arrange
        $this->expectException(UnreadableStream::class);
        $this->expectExceptionMessage('Cannot operate on a non-readable stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'streamGetContents',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'STDIO',
                'mode' => 'w',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://stdin',
            ]);

        $resource->expects($this->never())
            ->method('streamGetContents');

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://stdin', 'r'));

        $stream = new Stream($resource);

        // Act
        $stream->getContents();
    }

    public function testGetContentsThrowsUnreadableStreamIfStreamGetContentsReturnsFalse(): void
    {
        // Arrange
        $this->expectException(UnreadableStream::class);
        $this->expectExceptionMessage('Unreadable stream: Could not read stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'streamGetContents',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'STDIO',
                'mode' => 'r',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://stdin',
            ]);

        $resource->expects($this->once())
            ->method('streamGetContents')
            ->willReturn(false);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://stdin', 'r'));

        $stream = new Stream($resource);

        // Act
        $stream->getContents();
    }

    public function testToString(): void
    {
        // Arrange
        $expected = 'foo';

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fseek',
                'streamGetContents',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fseek')
            ->with(0)
            ->willReturn(0);

        $resource->expects($this->once())
            ->method('streamGetContents')
            ->willReturn($expected);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $result = (string) $stream;

        // Assert
        $this->assertSame($expected, $result);
    }

    public function testToStringReturnsExceptionMessageIfGetContentsFails(): void
    {
        // Arrange
        $expected = 'Unreadable stream: Cannot operate on a non-readable stream';

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fseek',
                'streamGetContents',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'STDIO',
                'mode' => 'w',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://stdin',
            ]);

        $resource->expects($this->once())
            ->method('fseek')
            ->with(0)
            ->willReturn(0);

        $resource->expects($this->never())
            ->method('streamGetContents');

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://stdin', 'r'));

        $stream = new Stream($resource);

        // Act
        $result = (string) $stream;

        // Assert
        $this->assertSame($expected, $result);
    }

    public function testGetSize(): void
    {
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'clearstatcache',
                'fstat',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->exactly(2))
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('clearstatcache')
            ->with(true, 'php://memory');

        $resource->expects($this->once())
            ->method('fstat')
            ->willReturn([
                'dev' => 16777220,
                'ino' => 0,
                'mode' => 33206,
                'nlink' => 1,
                'uid' => 501,
                'gid' => 20,
                'rdev' => 0,
                'size' => 42,
                'atime' => 1610612740,
                'mtime' => 1610612740,
                'ctime' => 1610612740,
                'blksize' => 4096,
                'blocks' => 0,
            ]);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $result = $stream->getSize();
        $second = $stream->getSize(); // Cached, so should not call fstat again.

        // Assert
        $this->assertSame(42, $result);
        $this->assertSame(42, $second);
    }

    public function testGetSizeReturnsNullIfSizeIsFalse(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'clearstatcache',
                'fstat',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->exactly(2))
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('clearstatcache')
            ->with(true, 'php://memory');

        $resource->expects($this->once())
            ->method('fstat')
            ->willReturn([
                'dev' => 16777220,
                'ino' => 0,
                'mode' => 33206,
                'nlink' => 1,
                'uid' => 501,
                'gid' => 20,
                'rdev' => 0,
                'size' => false,
                'atime' => 1610612740,
                'mtime' => 1610612740,
                'ctime' => 1610612740,
                'blksize' => 4096,
                'blocks' => 0,
            ]);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $result = $stream->getSize();

        // Assert
        $this->assertNull($result);
    }

    public function testGetSizeThrowsDetachedSourceIfStreamIsDetached(): void
    {
        // Arrange
        $this->expectException(DetachedSource::class);
        $this->expectExceptionMessage('Detached source: Cannot operate on a detached stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'clearstatcache',
                'fstat',
            ])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('clearstatcache');

        $resource->expects($this->never())
            ->method('fstat');

        $resource->expects($this->never())
            ->method('fclose');

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->detach();
        $stream->getSize();
    }

    public function testRead(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fread',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'r',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fread')
            ->with(42)
            ->willReturn('Hello World');

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $result = $stream->read(42);

        // Assert
        $this->assertSame('Hello World', $result);
    }

    public function testReadThrowsUnreadableStreamIfFReadReturnsFalse(): void
    {
        // Arrange
        $this->expectException(UnreadableStream::class);
        $this->expectExceptionMessage('Unable to read from stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fread',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'r',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fread')
            ->with(42)
            ->willReturn(false);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->read(42);
    }

    public function testReadThrowsUnreadableStreamIfFReadThrowsAnException(): void
    {
        // Arrange
        $this->expectException(UnreadableStream::class);
        $this->expectExceptionMessage('Unable to read from stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fread',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'r',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fread')
            ->with(42)
            ->willThrowException(new \RuntimeException('Failed to read from stream'));

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->read(42);
    }

    public function testReadReturnsEmptyStringIfLengthToReadIsZero(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fread',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'r',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('fread');

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $result = $stream->read(0);

        // Assert
        $this->assertSame('', $result);
    }

    public function testReadThrowsInvalidArgumentExceptionIfLengthToReadIsLessThanZero(): void
    {
        // Arrange
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Length to read must be greater than or equal to zero');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fread',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'r',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('fread');

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        $stream = new Stream($resource);

        // Act
        $stream->read(-1);
    }

    public function testReadThrowsUnreadableStreamIfStreamIsUnreadable(): void
    {
        // Arrange
        $this->expectException(UnreadableStream::class);
        $this->expectExceptionMessage('Cannot operate on a non-readable stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fread',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('fread');

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'w+'));

        $stream = new Stream($resource);

        // Act
        $stream->read(42);
    }

    public function testReadThrowsDetachedSourceIfStreamIsDetached(): void
    {
        // Arrange
        $this->expectException(DetachedSource::class);
        $this->expectExceptionMessage('Detached source: Cannot operate on a detached stream');

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fread',
            ])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('fread');

        $resource->expects($this->never())
            ->method('fclose');

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'w+'));

        $stream = new Stream($resource);

        // Act
        $stream->detach();
        $stream->read(42);
    }

    public function testWrite(): void
    {
        // Arrange
        $data = 'Hello World!';

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fwrite',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'w+'));

        $resource->expects($this->once())
            ->method('fwrite')
            ->with($data)
            ->willReturn(strlen($data));

        $stream = new Stream($resource);

        // Act
        $result = $stream->write($data);

        // Assert
        $this->assertSame(strlen($data), $result);
    }

    public function testWroteThrowsUnwritableStreamIfFWriteReturnsFalse(): void
    {
        // Arrange
        $this->expectException(UnwritableStream::class);
        $this->expectExceptionMessage('Unable to write to stream');

        $data = 'Hello World!';

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fwrite',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'w+'));

        $resource->expects($this->once())
            ->method('fwrite')
            ->with($data)
            ->willReturn(false);

        $stream = new Stream($resource);

        // Act
        $stream->write($data);
    }

    public function testWriteThrowsUnwritableStreamIfFWriteThrowsAnException(): void
    {
        // Arrange
        $this->expectException(UnwritableStream::class);
        $this->expectExceptionMessage('Unable to write to stream');

        $data = 'Hello World!';

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fwrite',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'w+'));

        $resource->expects($this->once())
            ->method('fwrite')
            ->with($data)
            ->willThrowException(new \Exception());

        $stream = new Stream($resource);

        // Act
        $stream->write($data);
    }

    public function testWriteThrowsUnwritableStreamIfResourceIsNotWritable(): void
    {
        // Arrange
        $this->expectException(UnwritableStream::class);
        $this->expectExceptionMessage('Unwritable stream: Cannot write to a non-writable stream');

        $data = 'Hello World!';

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fwrite',
            ])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'r',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r'));

        $resource->expects($this->never())
            ->method('fwrite');

        $stream = new Stream($resource);

        // Act
        $stream->write($data);
    }

    public function testWriteThrowsDetachedSourceIfStreamIsDetached(): void
    {
        // Arrange
        $this->expectException(DetachedSource::class);
        $this->expectExceptionMessage('Detached source: Cannot operate on a detached stream');

        $data = 'Hello World!';

        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'isResource',
                'streamGetMetaData',
                'fclose',
                'export',
                'fwrite',
            ])
            ->getMock();

        $resource->expects($this->once())
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => true,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->never())
            ->method('fclose');

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'w+'));

        $resource->expects($this->never())
            ->method('fwrite');

        $stream = new Stream($resource);

        // Act
        $stream->detach();
        $stream->write($data);
    }

    public function testCopyTo(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetMetaData', 'fclose', 'export', 'feof', 'fread'])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->exactly(2))
            ->method('feof')
            ->willReturnOnConsecutiveCalls(false, true);

        $resource->expects($this->once())
            ->method('fread')
            ->with(8192)
            ->willReturn('Hello World!');

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        /** @var Stream&\PHPUnit\Framework\MockObject\MockObject */
        $target = $this->getMockBuilder(Stream::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['write'])
            ->getMock();

        $target->expects($this->once())
            ->method('write')
            ->with('Hello World!')
            ->willReturn(12);

        $stream = new Stream($resource);

        // Act
        $stream->copyTo($target);
    }

    public function testCopyToBreaksSilentlyIfTargetFailsWrite(): void
    {
        // Arrange
        /** @var StreamResource&\PHPUnit\Framework\MockObject\MockObject */
        $resource = $this->getMockBuilder(StreamResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isResource', 'streamGetMetaData', 'fclose', 'export', 'feof', 'fread'])
            ->getMock();

        $resource->expects($this->exactly(2))
            ->method('isResource')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('streamGetMetaData')
            ->willReturn([
                'timed_out' => false,
                'blocked' => true,
                'eof' => false,
                'wrapper_type' => 'PHP',
                'stream_type' => 'MEMORY',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://memory',
            ]);

        $resource->expects($this->once())
            ->method('feof')
            ->willReturn(false);

        $resource->expects($this->once())
            ->method('fread')
            ->with(8192)
            ->willReturn('Hello World!');

        $resource->expects($this->once())
            ->method('fclose')
            ->willReturn(true);

        $resource->expects($this->once())
            ->method('export')
            ->willReturn(fopen('php://memory', 'r+'));

        /** @var Stream&\PHPUnit\Framework\MockObject\MockObject */
        $target = $this->getMockBuilder(Stream::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['write'])
            ->getMock();

        $target->expects($this->once())
            ->method('write')
            ->with('Hello World!')
            ->willReturn(0);

        $stream = new Stream($resource);

        // Act
        $stream->copyTo($target);
    }
}

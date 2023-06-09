<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\River;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use Arcanum\Flow\River\StreamResource;
use Arcanum\Flow\River\Stream;

#[CoversNothing]
final class IntegrationTest extends TestCase
{
    public function testStream(): void
    {
        $resource = fopen('php://memory', 'r+');
        if (!$resource) {
            $this->fail('Could not open memory stream');
        }
        $stream = new Stream(StreamResource::wrap($resource));
        $stream->write('Hello World');
        $stream->rewind();

        $this->assertSame('Hello World', $stream->read(11));
        $this->assertSame(11, $stream->tell());
        $this->assertSame(11, $stream->getSize());

        $stream->rewind();
        $this->assertSame(0, $stream->tell());
        $this->assertSame('Hello World', $stream->getContents());
        $this->assertSame(11, $stream->tell());

        $stream->seek(0);
        $this->assertSame('Hello World', (string)$stream);
        $this->assertSame(11, $stream->tell());

        $meta = $stream->getMetadata();

        if (!is_array($meta)) {
            $this->fail('Expected metadata to be an array');
        }

        $this->assertSame('php://memory', $meta['uri']);
        $this->assertSame('w+b', $meta['mode']);
        $this->assertSame('PHP', $meta['wrapper_type']);
        $this->assertSame('MEMORY', $meta['stream_type']);
        $this->assertNull($meta['size']);
        $this->assertTrue($meta['seekable']);
        $this->assertFalse($meta['timed_out']);
        $this->assertTrue($meta['blocked']);
        $this->assertSame(true, $meta['eof']);

        $this->assertSame('php://memory', $stream->getMetadata('uri'));

        $result = $stream->detach();
        $stream->close();

        $this->assertSame($resource, $result);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper;

use Arcanum\Hyper\RequestMethod;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RequestMethod::class)]
final class RequestMethodTest extends TestCase
{
    public function testRequestMethod(): void
    {
        $this->assertSame('GET', RequestMethod::GET->value);
        $this->assertSame('HEAD', RequestMethod::HEAD->value);
        $this->assertSame('POST', RequestMethod::POST->value);
        $this->assertSame('PUT', RequestMethod::PUT->value);
        $this->assertSame('DELETE', RequestMethod::DELETE->value);
        $this->assertSame('CONNECT', RequestMethod::CONNECT->value);
        $this->assertSame('OPTIONS', RequestMethod::OPTIONS->value);
        $this->assertSame('TRACE', RequestMethod::TRACE->value);
        $this->assertSame('PATCH', RequestMethod::PATCH->value);
    }
    public function testFromString(): void
    {
        $this->assertSame(RequestMethod::GET, RequestMethod::fromString('GET'));
        $this->assertSame(RequestMethod::HEAD, RequestMethod::fromString('HEAD'));
        $this->assertSame(RequestMethod::POST, RequestMethod::fromString('POST'));
        $this->assertSame(RequestMethod::PUT, RequestMethod::fromString('PUT'));
        $this->assertSame(RequestMethod::DELETE, RequestMethod::fromString('DELETE'));
        $this->assertSame(RequestMethod::CONNECT, RequestMethod::fromString('CONNECT'));
        $this->assertSame(RequestMethod::OPTIONS, RequestMethod::fromString('OPTIONS'));
        $this->assertSame(RequestMethod::TRACE, RequestMethod::fromString('TRACE'));
        $this->assertSame(RequestMethod::PATCH, RequestMethod::fromString('PATCH'));
    }

    public function testFromStringThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid request method: FOO');
        RequestMethod::fromString('FOO');
    }
}

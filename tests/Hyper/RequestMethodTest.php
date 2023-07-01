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
    public function testFrom(): void
    {
        $this->assertSame(RequestMethod::GET, RequestMethod::from('GET'));
        $this->assertSame(RequestMethod::HEAD, RequestMethod::from('HEAD'));
        $this->assertSame(RequestMethod::POST, RequestMethod::from('POST'));
        $this->assertSame(RequestMethod::PUT, RequestMethod::from('PUT'));
        $this->assertSame(RequestMethod::DELETE, RequestMethod::from('DELETE'));
        $this->assertSame(RequestMethod::CONNECT, RequestMethod::from('CONNECT'));
        $this->assertSame(RequestMethod::OPTIONS, RequestMethod::from('OPTIONS'));
        $this->assertSame(RequestMethod::TRACE, RequestMethod::from('TRACE'));
        $this->assertSame(RequestMethod::PATCH, RequestMethod::from('PATCH'));
    }
}

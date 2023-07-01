<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\URI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Hyper\URI\Spec;
use Arcanum\Hyper\URI\Authority;
use Arcanum\Hyper\URI\Fragment;
use Arcanum\Hyper\URI\Host;
use Arcanum\Hyper\URI\Path;
use Arcanum\Hyper\URI\Port;
use Arcanum\Hyper\URI\Query;
use Arcanum\Hyper\URI\Scheme;
use Arcanum\Hyper\URI\URI;
use Arcanum\Hyper\URI\UserInfo;
use Arcanum\Hyper\URI\MalformedURI;
use Arcanum\Gather\Registry;

#[CoversClass(Spec::class)]
#[UsesClass(Authority::class)]
#[UsesClass(Fragment::class)]
#[UsesClass(Host::class)]
#[UsesClass(Path::class)]
#[UsesClass(Port::class)]
#[UsesClass(Query::class)]
#[UsesClass(Scheme::class)]
#[UsesClass(URI::class)]
#[UsesClass(UserInfo::class)]
#[UsesClass(MalformedURI::class)]
final class SpecTest extends TestCase
{
    public function testFromServerParams(): void
    {
        // Arrange
        /** @var Registry&\PHPUnit\Framework\MockObject\MockObject */
        $serverParams = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['asString', 'has', 'asInt'])
            ->getMock();

        $serverParams->expects($this->exactly(4))
            ->method('asString')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTPS' => 'on',
                    'HTTP_HOST' => 'www.example.com',
                    'REQUEST_URI' => '/path/abc?query=foo#fragment',
                    'QUERY_STRING' => 'query=foo',
                    default => "Unexpected key: $key"
                }
            );

        $serverParams->expects($this->once())
            ->method('asInt')
            ->with('SERVER_PORT')
            ->willReturn(8080);

        $serverParams->expects($this->exactly(4))
            ->method('has')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTP_HOST' => true,
                    'SERVER_PORT' => true,
                    'REQUEST_URI' => true,
                    'QUERY_STRING' => true,
                    default => false
                }
            );

        // Act
        $uri = Spec::fromServerParams($serverParams);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('www.example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('https://www.example.com:8080/path/abc?query=foo#fragment', (string) $uri);
    }

    public function testParseURLWithIPv6(): void
    {
        // Arrange
        $url = 'https://[2001:db8:85a3:8d3:1319:8a2e:370:7348]:8080/path/abc?query=foo#fragment';

        // Act
        $uri = Spec::parseURL($url);

        // Assert
        if (!isset($uri['scheme'], $uri['host'], $uri['port'], $uri['path'], $uri['query'], $uri['fragment'])) {
            $this->fail('Expected scheme, host, port, path, query, and fragment to be set');
        }
        $this->assertEquals('https', $uri['scheme']);
        $this->assertEquals('[2001:db8:85a3:8d3:1319:8a2e:370:7348]', $uri['host']);
        $this->assertEquals(8080, $uri['port']);
        $this->assertEquals('/path/abc', $uri['path']);
        $this->assertEquals('query=foo', $uri['query']);
        $this->assertEquals('fragment', $uri['fragment']);
    }

    public function testParseFullRULThrowsMalformedURIIfNotParsable(): void
    {
        // Assert
        $this->expectException(MalformedURI::class);

        // Act
        Spec::parseFullURL(':2019');
    }


    public function testFromServerParamsNoQuery(): void
    {
        // Arrange
        /** @var Registry&\PHPUnit\Framework\MockObject\MockObject */
        $serverParams = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['asString', 'has', 'asInt'])
            ->getMock();

        $serverParams->expects($this->exactly(3))
            ->method('asString')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTPS' => 'on',
                    'HTTP_HOST' => 'www.example.com',
                    'REQUEST_URI' => '/path/abc#fragment',
                    default => "Unexpected key: $key"
                }
            );

        $serverParams->expects($this->once())
            ->method('asInt')
            ->with('SERVER_PORT')
            ->willReturn(8080);

        $serverParams->expects($this->exactly(4))
            ->method('has')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTP_HOST' => true,
                    'SERVER_PORT' => true,
                    'REQUEST_URI' => true,
                    'QUERY_STRING' => false,
                    default => false
                }
            );

        // Act
        $uri = Spec::fromServerParams($serverParams);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('www.example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('https://www.example.com:8080/path/abc#fragment', (string) $uri);
    }

    public function testFromServerParamsNoHttpHostServerNameOrServerAddr(): void
    {
        // Arrange
        /** @var Registry&\PHPUnit\Framework\MockObject\MockObject */
        $serverParams = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['asString', 'has', 'asInt'])
            ->getMock();

        $serverParams->expects($this->exactly(3))
            ->method('asString')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTPS' => 'on',
                    'REQUEST_URI' => '/path/abc?query=foo#fragment',
                    'QUERY_STRING' => 'query=foo',
                    default => "Unexpected key: $key"
                }
            );

        $serverParams->expects($this->once())
            ->method('asInt')
            ->with('SERVER_PORT')
            ->willReturn(8080);

        $serverParams->expects($this->exactly(6))
            ->method('has')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTP_HOST' => false,
                    'SERVER_NAME' => false,
                    'SERVER_ADDR' => false,
                    'SERVER_PORT' => true,
                    'REQUEST_URI' => true,
                    'QUERY_STRING' => true,
                    default => false
                }
            );

        // Act
        $uri = Spec::fromServerParams($serverParams);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('localhost', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('https://localhost:8080/path/abc?query=foo#fragment', (string) $uri);
    }

    public function testFromServerParamsNoHttpHostServerNameRequestURIOrServerAddr(): void
    {
        // Arrange
        /** @var Registry&\PHPUnit\Framework\MockObject\MockObject */
        $serverParams = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['asString', 'has', 'asInt'])
            ->getMock();

        $serverParams->expects($this->exactly(2))
            ->method('asString')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTPS' => 'on',
                    'QUERY_STRING' => 'query=foo',
                    default => "Unexpected key: $key"
                }
            );

        $serverParams->expects($this->once())
            ->method('asInt')
            ->with('SERVER_PORT')
            ->willReturn(8080);

        $serverParams->expects($this->exactly(6))
            ->method('has')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTP_HOST' => false,
                    'SERVER_NAME' => false,
                    'SERVER_ADDR' => false,
                    'SERVER_PORT' => true,
                    'REQUEST_URI' => false,
                    'QUERY_STRING' => true,
                    default => false
                }
            );

        // Act
        $uri = Spec::fromServerParams($serverParams);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('localhost', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('', $uri->getPath());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('', $uri->getFragment());
        $this->assertSame('https://localhost:8080?query=foo', (string) $uri);
    }

    public function testFromServerParamsNoHttpHostServerNameRequestURIQueryOrServerAddr(): void
    {
        // Arrange
        /** @var Registry&\PHPUnit\Framework\MockObject\MockObject */
        $serverParams = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['asString', 'has', 'asInt'])
            ->getMock();

        $serverParams->expects($this->once())
            ->method('asString')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTPS' => 'off',
                    default => "Unexpected key: $key"
                }
            );

        $serverParams->expects($this->once())
            ->method('asInt')
            ->with('SERVER_PORT')
            ->willReturn(80);

        $serverParams->expects($this->exactly(6))
            ->method('has')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTP_HOST' => false,
                    'SERVER_NAME' => false,
                    'SERVER_ADDR' => false,
                    'SERVER_PORT' => true,
                    'REQUEST_URI' => false,
                    'QUERY_STRING' => false,
                    default => false
                }
            );

        // Act
        $uri = Spec::fromServerParams($serverParams);

        // Assert
        $this->assertSame('http', $uri->getScheme());
        $this->assertSame('localhost', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertSame('', $uri->getPath());
        $this->assertSame('', $uri->getQuery());
        $this->assertSame('', $uri->getFragment());
        $this->assertSame('http://localhost', (string) $uri);
    }

    public function testFromServerParamsHTTPHostIncludesPort(): void
    {
        // Arrange
        /** @var Registry&\PHPUnit\Framework\MockObject\MockObject */
        $serverParams = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['asString', 'has', 'asInt'])
            ->getMock();

        $serverParams->expects($this->exactly(4))
            ->method('asString')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTPS' => 'on',
                    'HTTP_HOST' => 'www.example.com:8080',
                    'REQUEST_URI' => '/path/abc?query=foo#fragment',
                    'QUERY_STRING' => 'query=foo',
                    default => "Unexpected key: $key"
                }
            );

        $serverParams->expects($this->never())
            ->method('asInt')
            ->with('SERVER_PORT');

        $serverParams->expects($this->exactly(4))
            ->method('has')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTP_HOST' => true,
                    'REQUEST_URI' => true,
                    'SERVER_PORT' => false,
                    'QUERY_STRING' => true,
                    default => false
                }
            );

        // Act
        $uri = Spec::fromServerParams($serverParams);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('www.example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('https://www.example.com:8080/path/abc?query=foo#fragment', (string) $uri);
    }


    public function testFromServerParamsNoHTTPHostButServerNameIsSet(): void
    {
        // Arrange
        /** @var Registry&\PHPUnit\Framework\MockObject\MockObject */
        $serverParams = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['asString', 'has', 'asInt'])
            ->getMock();

        $serverParams->expects($this->exactly(4))
            ->method('asString')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTPS' => 'on',
                    'SERVER_NAME' => 'www.example.com',
                    'REQUEST_URI' => '/path/abc?query=foo#fragment',
                    'QUERY_STRING' => 'query=foo',
                    default => "Unexpected key: $key"
                }
            );

        $serverParams->expects($this->once())
            ->method('asInt')
            ->with('SERVER_PORT')
            ->willReturn(8080);

        $serverParams->expects($this->exactly(5))
            ->method('has')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTP_HOST' => false,
                    'SERVER_NAME' => true,
                    'SERVER_PORT' => true,
                    'REQUEST_URI' => true,
                    'QUERY_STRING' => true,
                    default => false
                }
            );

        // Act
        $uri = Spec::fromServerParams($serverParams);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('www.example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('https://www.example.com:8080/path/abc?query=foo#fragment', (string) $uri);
    }

    public function testFromServerParamsNoHTTPHostOrServerNameButServerAddrIsSet(): void
    {
        // Arrange
        /** @var Registry&\PHPUnit\Framework\MockObject\MockObject */
        $serverParams = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['asString', 'has', 'asInt'])
            ->getMock();

        $serverParams->expects($this->exactly(4))
            ->method('asString')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTPS' => 'on',
                    'SERVER_ADDR' => '192.168.0.1',
                    'REQUEST_URI' => '/path/abc?query=foo#fragment',
                    'QUERY_STRING' => 'query=foo',
                    default => "Unexpected key: $key"
                }
            );

        $serverParams->expects($this->once())
            ->method('asInt')
            ->with('SERVER_PORT')
            ->willReturn(8080);

        $serverParams->expects($this->exactly(6))
            ->method('has')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTP_HOST' => false,
                    'SERVER_NAME' => false,
                    'SERVER_ADDR' => true,
                    'SERVER_PORT' => true,
                    'REQUEST_URI' => true,
                    'QUERY_STRING' => true,
                    default => false
                }
            );

        // Act
        $uri = Spec::fromServerParams($serverParams);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('192.168.0.1', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('https://192.168.0.1:8080/path/abc?query=foo#fragment', (string) $uri);
    }

    public function testFromServerParamsQueryString(): void
    {
        // Arrange
        /** @var Registry&\PHPUnit\Framework\MockObject\MockObject */
        $serverParams = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['asString', 'has', 'asInt'])
            ->getMock();

        $serverParams->expects($this->exactly(4))
            ->method('asString')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTPS' => 'on',
                    'HTTP_HOST' => 'www.example.com',
                    'REQUEST_URI' => '/path/abc#fragment',
                    'QUERY_STRING' => 'query=foo',
                    default => "Unexpected key: $key"
                }
            );

        $serverParams->expects($this->once())
            ->method('asInt')
            ->with('SERVER_PORT')
            ->willReturn(8080);

        $serverParams->expects($this->exactly(4))
            ->method('has')
            ->willReturnCallback(
                fn (string $key) => match ($key) {
                    'HTTP_HOST' => true,
                    'SERVER_PORT' => true,
                    'REQUEST_URI' => true,
                    'QUERY_STRING' => true,
                    default => false
                }
            );

        // Act
        $uri = Spec::fromServerParams($serverParams);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('www.example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('https://www.example.com:8080/path/abc?query=foo#fragment', (string) $uri);
    }
}

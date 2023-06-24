<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\URI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Hyper\URI\URI;
use Arcanum\Hyper\URI\Authority;
use Arcanum\Hyper\URI\Fragment;
use Arcanum\Hyper\URI\Host;
use Arcanum\Hyper\URI\Path;
use Arcanum\Hyper\URI\Port;
use Arcanum\Hyper\URI\Query;
use Arcanum\Hyper\URI\Scheme;
use Arcanum\Hyper\URI\UserInfo;
use Arcanum\Hyper\URI\MalformedURI;
use Arcanum\Hyper\URI\Spec;

#[CoversClass(URI::class)]
#[UsesClass(Authority::class)]
#[UsesClass(Fragment::class)]
#[UsesClass(Host::class)]
#[UsesClass(Path::class)]
#[UsesClass(Port::class)]
#[UsesClass(Query::class)]
#[UsesClass(Scheme::class)]
#[UsesClass(UserInfo::class)]
#[UsesClass(Spec::class)]
#[UsesClass(MalformedURI::class)]
final class URITest extends TestCase
{
    public function testNewURI(): void
    {
        // Arrange
        $url = 'https://user:pass@www.example.com:8080/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('www.example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('user:pass', $uri->getUserInfo());
        $this->assertSame($url, (string) $uri);
        $this->assertSame($url, (string) $uri);
    }

    public function testNewURIWithIPv6(): void
    {
        // Arrange
        $url = 'https://[::1]:8080/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('[::1]', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame($url, (string) $uri);
    }

    public function testNewURIWithMalformedURIThrows(): void
    {
        // Arrange
        $url = 'scheme://example_login:!#Password?@ZZZ@127.0.0.1/some_path';

        // Assert
        $this->expectException(MalformedURI::class);

        // Act
        new URI($url);
    }

    public function testRelativeURIMustNotHaveAColonInTheFirstSegment(): void
    {
        // Arrange
        $url = '::1:8080/path/abc?query=foo#fragment';

        // Assert
        $this->expectException(MalformedURI::class);

        // Act
        new URI($url);
    }

    public function testWithUserInfo(): void
    {
        // Arrange
        $url = 'https://user:pass@www.example.com:8080/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Act
        $newURI = $uri->withUserInfo('newuser', 'newpass');

        // Assert
        $this->assertSame('newuser:newpass', $newURI->getUserInfo());
        $this->assertSame('https://user:pass@www.example.com:8080/path/abc?query=foo#fragment', (string) $uri);
        $this->assertSame('https://newuser:newpass@www.example.com:8080/path/abc?query=foo#fragment', (string) $newURI);
    }

    public function testWithScheme(): void
    {
        // Arrange
        $url = 'https://www.example.com:8080/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Act
        $newURI = $uri->withScheme('http');

        // Assert
        $this->assertSame('http', $newURI->getScheme());
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('http://www.example.com:8080/path/abc?query=foo#fragment', (string) $newURI);
    }

    public function testWithHost(): void
    {
        // Arrange
        $url = 'https://www.example.com:8080/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Act
        $newURI = $uri->withHost('www.example.org');

        // Assert
        $this->assertSame('www.example.org', $newURI->getHost());
        $this->assertSame('www.example.com', $uri->getHost());
        $this->assertSame('https://www.example.org:8080/path/abc?query=foo#fragment', (string) $newURI);
    }

    public function testWithPort(): void
    {
        // Arrange
        $url = 'https://www.example.com:8080/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Act
        $newURI = $uri->withPort(80);

        // Assert
        $this->assertSame(80, $newURI->getPort());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('https://www.example.com:80/path/abc?query=foo#fragment', (string) $newURI);
    }

    public function testWithPath(): void
    {
        // Arrange
        $url = 'https://www.example.com:8080/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Act
        $newURI = $uri->withPath('/new/path');

        // Assert
        $this->assertSame('/new/path', $newURI->getPath());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('https://www.example.com:8080/new/path?query=foo#fragment', (string) $newURI);
    }

    public function testWithQuery(): void
    {
        // Arrange
        $url = 'https://www.example.com:8080/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Act
        $newURI = $uri->withQuery('newquery=bar');

        // Assert
        $this->assertSame('newquery=bar', $newURI->getQuery());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('https://www.example.com:8080/path/abc?newquery=bar#fragment', (string) $newURI);
    }

    public function testWithFragment(): void
    {
        // Arrange
        $url = 'https://www.example.com:8080/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Act
        $newURI = $uri->withFragment('newfragment');

        // Assert
        $this->assertSame('newfragment', $newURI->getFragment());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('https://www.example.com:8080/path/abc?query=foo#newfragment', (string) $newURI);
    }

    public function testWithHostSettingEmptyHostWillDefaultToLocalHostOnWebSchemes(): void
    {
        // Arrange
        $url = 'https://www.example.com:8080/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Act
        $newURI = $uri->withHost('');

        // Assert
        $this->assertSame('localhost', $newURI->getHost());
        $this->assertSame('www.example.com', $uri->getHost());
        $this->assertSame('https://localhost:8080/path/abc?query=foo#fragment', (string) $newURI);
    }

    public function testStripsDefaultPort(): void
    {
        // Arrange
        $url = 'https://user:pass@www.example.com:443/path/abc?query=foo#fragment';
        $uri = new URI($url);

        // Assert
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('www.example.com', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertSame('/path/abc', $uri->getPath());
        $this->assertSame('query=foo', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
        $this->assertSame('user:pass', $uri->getUserInfo());
        $this->assertSame('https://user:pass@www.example.com/path/abc?query=foo#fragment', (string) $uri);
    }
}

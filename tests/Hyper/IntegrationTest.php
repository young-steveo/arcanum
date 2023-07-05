<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Hyper\Server;
use Arcanum\Hyper\PHPServerAdapter;
use Arcanum\Hyper\ServerRequest;
use Arcanum\Hyper\Request;
use Arcanum\Hyper\RequestMethod;
use Arcanum\Hyper\Version;
use Arcanum\Flow\River\CachingStream;
use Arcanum\Flow\River\LazyResource;
use Arcanum\Flow\River\Stream;
use Arcanum\Flow\River\StreamResource;
use Arcanum\Flow\River\TemporaryStream;
use Arcanum\Gather\IgnoreCaseRegistry;
use Arcanum\Gather\Registry;
use Arcanum\Hyper\Headers;
use Arcanum\Hyper\Message;
use Arcanum\Hyper\URI\Authority;
use Arcanum\Hyper\URI\Fragment;
use Arcanum\Hyper\URI\Host;
use Arcanum\Hyper\URI\Path;
use Arcanum\Hyper\URI\Port;
use Arcanum\Hyper\URI\Query;
use Arcanum\Hyper\URI\Scheme;
use Arcanum\Hyper\URI\Spec;
use Arcanum\Hyper\URI\URI;
use Arcanum\Hyper\URI\UserInfo;
use Arcanum\Hyper\Files\UploadedFiles;

#[CoversClass(Request::class)]
#[CoversClass(ServerRequest::class)]
#[CoversClass(Server::class)]
#[UsesClass(RequestMethod::class)]
#[UsesClass(LazyResource::class)]
#[UsesClass(Stream::class)]
#[UsesClass(StreamResource::class)]
#[UsesClass(IgnoreCaseRegistry::class)]
#[UsesClass(Registry::class)]
#[UsesClass(Headers::class)]
#[UsesClass(Message::class)]
#[UsesClass(Authority::class)]
#[UsesClass(Fragment::class)]
#[UsesClass(Host::class)]
#[UsesClass(Path::class)]
#[UsesClass(Port::class)]
#[UsesClass(Query::class)]
#[UsesClass(Scheme::class)]
#[UsesClass(Spec::class)]
#[UsesClass(URI::class)]
#[UsesClass(UserInfo::class)]
#[UsesClass(CachingStream::class)]
#[UsesClass(TemporaryStream::class)]
#[UsesClass(UploadedFiles::class)]
#[UsesClass(Version::class)]
#[UsesClass(PHPServerAdapter::class)]
final class IntegrationTest extends TestCase
{
    public function testRequestToStringWithAllData(): void
    {
        // Arrange
        $headers = new Headers([
            'User-Agent' => ['Arcanum/0.1.0'],
            'Host' => ['example.com'],
            'Accept' => ['text/html'],
            'Set-Cookie' => ['foo=bar', 'baz=qux'],
        ]);

        $resource = LazyResource::for('php://memory', 'w+');
        $resource->fwrite('{"foo":"bar"}');
        $resource->fseek(0);
        $body = new Stream($resource);
        $request = new Request(
            new Message(
                $headers,
                $body,
                Version::v11,
            ),
            RequestMethod::GET,
            new URI('https://example.com/foo?bar=baz#qux'),
        );

        // Act
        $string = (string) $request;

        // Assert
        $this->assertSame(
            "GET /foo?bar=baz HTTP/1.1\r\n" .
            "Host: example.com\r\n" .
            "Set-Cookie: foo=bar\r\n" .
            "Set-Cookie: baz=qux\r\n" .
            "User-Agent: Arcanum/0.1.0\r\n" .
            "Accept: text/html\r\n" .
            "\r\n" .
            '{"foo":"bar"}',
            $string,
        );
    }

    public function testServerRequestFromGlobals(): void
    {
        // Arrange
        $originalServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/foo?bar=baz#qux';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTP_USER_AGENT'] = 'Arcanum/0.1.0';
        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $_SERVER['HTTP_COOKIE'] = 'foo=bar; baz=qux';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['CONTENT_LENGTH'] = '13';

        $server = new Server(new PHPServerAdapter());

        // Act
        $request = $server->request();

        // Assert
        $this->assertInstanceOf(ServerRequest::class, $request);
        $this->assertSame(
            "GET /foo?bar=baz HTTP/1.1\r\n" .
            "Host: example.com\r\n" .
            "User-Agent: Arcanum/0.1.0\r\n" .
            "Accept: text/html\r\n" .
            "Cookie: foo=bar; baz=qux\r\n" .
            "Content-Type: application/json\r\n" .
            "Content-Length: 13\r\n" .
            "\r\n",
            (string) $request,
        );

        // Clean up
        $_SERVER = $originalServer;
    }
}

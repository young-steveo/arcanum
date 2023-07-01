<?php

declare(strict_types=1);

namespace Arcanum\Hyper;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;
use Arcanum\Flow\River\CachingStream;
use Arcanum\Flow\River\Stream;
use Arcanum\Flow\River\LazyResource;
use Arcanum\Gather\Registry;
use Arcanum\Hyper\URI\Spec;
use Arcanum\Hyper\Files\UploadedFiles;

class ServerRequest implements ServerRequestInterface, \Stringable
{
    /**
     * @var array<string, string>
     */
    protected array $cookieParams = [];

    /**
     * @var array<string, mixed>
     */
    protected array $queryParams = [];

    /**
     * @var mixed[]
     */
    protected array $attributes = [];

    /**
     * @var array<string|int,mixed>|object|null
     */
    protected array|object|null $parsedBody = null;

    public function __construct(
        protected RequestInterface $request,
        protected Registry $serverParams,
        protected UploadedFiles|null $uploadedFiles = null,
    ) {
    }

    public static function fromGlobals(): ServerRequestInterface
    {

        $serverParams = new Registry($_SERVER);
        $method = RequestMethod::from($serverParams->asString('REQUEST_METHOD', 'GET'));
        $headers = new Headers(getallheaders());
        $uri = Spec::fromServerParams($serverParams);
        $body = CachingStream::fromStream(new Stream(LazyResource::for('php://input', 'r+')));
        $protocolVersion = Version::from(
            str_replace('HTTP/', '', $serverParams->asString('SERVER_PROTOCOL', '1.1'))
        );

        $message = new Message($headers, $body, $protocolVersion);
        $request = new Request($message, $method, $uri);
        $serverRequest = new self($request, $serverParams);

        return $serverRequest
            ->withCookieParams($_COOKIE)
            ->withQueryParams($_GET)
            ->withParsedBody($_POST)
            ->withUploadedFiles(UploadedFiles::fromSuperGlobal()->toArray());
    }

    /**
     * Retrieve server parameters.
     *
     * @return array<string, mixed>
     */
    public function getServerParams(): array
    {
        return $this->serverParams->toArray();
    }

    /**
     * Retrieve uploaded files.
     *
     * @return array<string, mixed>
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles?->toArray() ?? [];
    }

    /**
     * Get a new instance with the specified uploaded files.
     *
     * @param array<string, mixed> $uploadedFiles
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $new = clone $this;
        $new->uploadedFiles = UploadedFiles::fromArray($uploadedFiles);
        return $new;
    }

    /**
     * Retrieve cookies.
     *
     * @return array<string, string>
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * Get a new instance with the specified cookies.
     *
     * @param array<string, string> $cookies
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }

    /**
     * Retrieve query string arguments.
     *
     * @return array<string, mixed>
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Get a new instance with the specified query string arguments.
     *
     * @param array<string, mixed> $query
     */
    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * @return mixed[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Get a new instance with the specified derived request attribute.
     *
     * @param string $name
     * @param mixed $value
     */
    public function withAttribute(string $name, mixed $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    /**
     * Get a new instance that removes the specified derived request attribute.
     *
     * @param string $name
     */
    public function withoutAttribute(string $name): ServerRequestInterface
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }

    /**
     * Retrieve the deserialized body parameters, if any.
     *
     * @return array<string|int,mixed>|object|null
     */
    public function getParsedBody(): array|object|null
    {
        return $this->parsedBody;
    }

    /**
     * Return an instance with the specified body parameters.
     *
     * @param array<string|int,mixed>|object|null $data
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    /**
     * RequestInterface methods
     */

    /**
     * Retrieve the message's request target.
     * @return string
     */
    public function getRequestTarget(): string
    {
        return $this->request->getRequestTarget();
    }

    /**
     * Return an instance with the specific request-target.
     */
    public function withRequestTarget(string $requestTarget): ServerRequestInterface
    {
        $new = clone $this;
        $new->request = $this->request->withRequestTarget($requestTarget);
        return $new;
    }

    /**
     * Retrieve the HTTP method of the request.
     */
    public function getMethod(): string
    {
        return $this->request->getMethod();
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * @param string $method
     */
    public function withMethod($method): ServerRequestInterface
    {
        $new = clone $this;
        $new->request = $this->request->withMethod($method);
        return $new;
    }

    /**
     * Retrieve the URI instance.
     */
    public function getUri(): UriInterface
    {
        return $this->request->getUri();
    }

    /**
     * Return an instance with the provided URI.
     *
     * @param UriInterface $uri
     */
    public function withUri(UriInterface $uri, $preserveHost = false): ServerRequestInterface
    {
        $new = clone $this;
        $new->request = $this->request->withUri($uri, $preserveHost);
        return $new;
    }

    /**
     * Retrieve the HTTP protocol version as a string.
     */
    public function getProtocolVersion(): string
    {
        return $this->request->getProtocolVersion();
    }

    /**
     * Return an instance with the provided HTTP protocol version.
     *
     * @param string $version
     */
    public function withProtocolVersion($version): ServerRequestInterface
    {
        $new = clone $this;
        $new->request = $this->request->withProtocolVersion($version);
        return $new;
    }

    /**
     * Retrieve all message header values.
     *
     * @return string[][]
     */
    public function getHeaders(): array
    {
        return $this->request->getHeaders();
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name
     */
    public function hasHeader(string $name): bool
    {
        return $this->request->hasHeader($name);
    }

    /**
     * Retrieve a message header value by the given case-insensitive name.
     *
     * @param string $name
     * @return string[]
     */
    public function getHeader(string $name): array
    {
        return $this->request->getHeader($name);
    }

    /**
     * Retrieve a comma-separated string of the values for a single header.
     *
     * @param string $name
     */
    public function getHeaderLine(string $name): string
    {
        return $this->request->getHeaderLine($name);
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * @param string $name
     * @param string|string[] $value
     */
    public function withHeader(string $name, $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->request = $this->request->withHeader($name, $value);
        return $new;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * @param string $name
     * @param string|string[] $value
     */
    public function withAddedHeader(string $name, $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->request = $this->request->withAddedHeader($name, $value);
        return $new;
    }

    /**
     * Return an instance without the specified header.
     *
     * @param string $name
     */
    public function withoutHeader(string $name): ServerRequestInterface
    {
        $new = clone $this;
        $new->request = $this->request->withoutHeader($name);
        return $new;
    }

    /**
     * Retrieve the body of the message.
     */
    public function getBody(): StreamInterface
    {
        return $this->request->getBody();
    }

    /**
     * Return an instance with the specified message body.
     *
     * @param StreamInterface $body
     */
    public function withBody(StreamInterface $body): ServerRequestInterface
    {
        $new = clone $this;
        $new->request = $this->request->withBody($body);
        return $new;
    }

    /**
     * Get the request message as a string
     */
    public function __toString(): string
    {
        if ($this->request instanceof \Stringable) {
            return (string)$this->request;
        }
        return $this->request->getBody()->getContents();
    }
}

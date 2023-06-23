<?php

declare(strict_types=1);

namespace Arcanum\Hyper;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Arcanum\Flow\River\CachingStream;
use Arcanum\Flow\River\Stream;
use Arcanum\Flow\River\LazyResource;
use Arcanum\Gather\Registry;
use Arcanum\Hyper\URI\Spec;
use Arcanum\Hyper\Files\UploadedFiles;

class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * Uploaded files.
     */
    protected UploadedFiles|null $uploadedFiles;

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
        protected Headers $headers,
        protected StreamInterface $body,
        protected Version $protocolVersion,
        protected RequestMethod $method,
        protected UriInterface $uri,
        protected Registry $serverParams,
    ) {
        parent::__construct($headers, $body, $protocolVersion, $method, $uri);
    }

    public static function fromGlobals(): ServerRequestInterface
    {

        $serverParams = new Registry($_SERVER);
        $method = RequestMethod::fromString($serverParams->asString('REQUEST_METHOD', 'GET'));
        $headers = new Headers(getallheaders());
        $uri = Spec::fromServerParams($serverParams);
        $body = CachingStream::fromStream(new Stream(LazyResource::for('php://input', 'r+')));
        $protocolVersion = Version::fromString(
            str_replace('HTTP/', '', $serverParams->asString('SERVER_PROTOCOL', '1.1'))
        );

        $request = new self($headers, $body, $protocolVersion, $method, $uri, $serverParams);

        return $request
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
}

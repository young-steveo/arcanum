<?php

declare(strict_types=1);

namespace Arcanum\Hyper;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @todo Prevent CRLF injection in headers.
 */


class Request extends Message implements RequestInterface
{
    /**
     * If this is set, it will override the request target of the URI.
     */
    protected string|null $requestTarget = null;

    public function __construct(
        protected Headers $headers,
        protected StreamInterface $body,
        protected Version $protocolVersion,
        protected RequestMethod $method,
        protected UriInterface $uri,
    ) {
        parent::__construct($headers, $body, $protocolVersion);
        if ($this->headers->has('Host')) {
            $this->setHostHeaderFromURI();
        }
    }

    /**
     * Clone the request.
     */
    public function __clone()
    {
        $this->headers = clone $this->headers;
    }

    /**
     * Set the Host header from the URI, if possible.
     */
    protected function setHostHeaderFromURI(): void
    {
        $host = $this->uri->getHost();

        if ($host === '') {
            return;
        }

        $port = $this->uri->getPort();

        if ($port !== null) {
            $host .= ":$port";
        }

        $this->headers['Host'] = [$host];
    }

    /**
     * Retrieves the message's request target.
     */
    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if ($target === '') {
            $target = '/';
        }

        $query = $this->uri->getQuery();
        if ($query !== '') {
            $target .= "?$query";
        }

        $this->requestTarget = $target;

        return $target;
    }

    /**
     * Return an instance with the specific request-target.
     */
    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        // @todo: validate request target forms against http://tools.ietf.org/html/rfc7230#section-5.3
        if (preg_match('/\s/', $requestTarget)) {
            throw new \InvalidArgumentException('Request target cannot contain whitespace');
        }
        $request = clone $this;
        $request->requestTarget = $requestTarget;
        return $request;
    }


    /**
     * Retrieves the HTTP method of the request.
     */
    public function getMethod(): string
    {
        return $this->method->value;
    }

    /**
     * Return an instance with the provided HTTP method.
     */
    public function withMethod(string $method): RequestInterface
    {
        $request = clone $this;
        $request->method = RequestMethod::fromString($method);
        return $request;
    }

    /**
     * Retrieves the URI instance.
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Returns an instance with the provided URI.
     */
    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        if ($uri === $this->uri) {
            return $this;
        }

        $request = clone $this;
        $request->uri = $uri;

        if (!$preserveHost || $this->headers->get('Host') === null) {
            $request->setHostHeaderFromURI();
        }

        return $request;
    }
}

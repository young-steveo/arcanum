<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

use Arcanum\Gather\Registry;
use Psr\Http\Message\UriInterface;

class URI implements UriInterface
{
    protected Scheme $scheme;
    protected Authority $authority;
    protected Path $path;
    protected Query $query;
    protected Fragment $fragment;

    /**
     * @var string|null The composed URI after normalization.
     */
    protected string|null $composed = null;

    public function __construct(protected string $uri = '')
    {
        $parsed = Spec::parse($uri);

        $this->scheme = $parsed['scheme'];
        $this->authority = new Authority(
            $parsed['user'],
            $parsed['pass'],
            $parsed['host'],
            $parsed['port'],
        );
        $this->path = $parsed['path'];
        $this->query = $parsed['query'];
        $this->fragment = $parsed['fragment'];

        $this->finalize();
    }

    public function __toString(): string
    {
        if ($this->composed !== null) {
            return $this->composed;
        }

        $this->composed = '';

        if ($this->scheme !== '') {
            $this->composed .= $this->scheme . ':';
        }

        $authority = $this->getAuthority();

        if ($authority !== '' || (string)$this->scheme === 'file') {
            $this->composed .= '//' . $authority;
        }

        $this->composed .= $this->path;

        if ($this->query !== '') {
            $this->composed .= '?' . $this->query;
        }

        if ($this->fragment !== '') {
            $this->composed .= '#' . $this->fragment;
        }

        return $this->composed;
    }

    public function getScheme(): string
    {
        return (string)$this->scheme;
    }

    public function getAuthority(): string
    {
        return (string)$this->authority;
    }

    public function getUserInfo(): string
    {
        return $this->authority->getUserInfo();
    }

    public function getHost(): string
    {
        return (string)$this->authority->getHost();
    }

    public function getPort(): int|null
    {
        $port = $this->authority->getPort();
        return $port === null ? null : (int)(string)$port;
    }

    public function getPath(): string
    {
        return (string)$this->path;
    }

    public function getQuery(): string
    {
        return (string)$this->query;
    }

    public function getFragment(): string
    {
        return (string)$this->fragment;
    }

    public function withScheme($scheme): UriInterface
    {
        $clone = clone $this;
        $clone->scheme = new Scheme($scheme);
        $clone->composed = null;
        $clone->finalize();
        return $clone;
    }

    public function withUserInfo($user, $password = null): UriInterface
    {
        $clone = clone $this;
        $clone->authority = $this->authority->withUserInfo(
            new UserInfo($user),
            new UserInfo($password ?? '')
        );
        $clone->composed = null;
        $clone->finalize();
        return $clone;
    }

    public function withHost($host): UriInterface
    {
        $clone = clone $this;
        $clone->authority = $this->authority->withHost(new Host($host));
        $clone->composed = null;
        $clone->finalize();
        return $clone;
    }

    public function withPort($port): UriInterface
    {
        $clone = clone $this;
        $clone->authority = $this->authority->withPort(new Port($port));
        $clone->composed = null;
        $clone->finalize();
        return $clone;
    }

    public function withPath($path): UriInterface
    {
        $clone = clone $this;
        $clone->path = new Path($path);
        $clone->composed = null;
        $clone->finalize();
        return $clone;
    }

    public function withQuery($query): UriInterface
    {
        $clone = clone $this;
        $clone->query = new Query($query);
        $clone->composed = null;
        $clone->finalize();
        return $clone;
    }

    public function withFragment($fragment): UriInterface
    {
        $clone = clone $this;
        $clone->fragment = new Fragment($fragment);
        $clone->composed = null;
        $clone->finalize();
        return $clone;
    }

    protected function finalize(): void
    {
        if (Spec::hasDefaultPort($this)) {
            $this->authority = $this->authority->withPort(null);
        }

        if ($this->authority->getHost()->isEmpty() && $this->scheme->isWebScheme()) {
            $this->authority = $this->authority->withHost(Host::localhost());
        }

        if (empty($this->getAuthority())) {
            if ($this->scheme->isEmpty() && strpos(explode('/', (string)$this->path)[0], ':') !== false) {
                throw new MalformedURI("A relative URI must not have a colon ':' in the first path segment");
            }
        }
    }
}

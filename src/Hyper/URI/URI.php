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
        $parsed = self::parse($uri);

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

    public static function fromServerParams(Registry $serverParams): self
    {
        $https = $serverParams->asString('HTTPS', 'off');
        $uri = (new self())->withScheme($https !== 'off' ? 'https' : 'http');

        $port = null;
        if ($serverParams->has('HTTP_HOST')) {
            $authority = Authority::fromAuthorityString($serverParams->asString('HTTP_HOST'));
            $uri = $uri->withHost((string)$authority->getHost());
            $port = $authority->getPort();
        } elseif ($serverParams->has('SERVER_NAME')) {
            $uri = $uri->withHost($serverParams->asString('SERVER_NAME'));
        } elseif ($serverParams->has('SERVER_ADDR')) {
            $uri = $uri->withHost($serverParams->asString('SERVER_ADDR'));
        }
        if ($port === null && $serverParams->has('SERVER_PORT')) {
            $port = new Port($serverParams->asInt('SERVER_PORT'));
        }
        $uri = $uri->withPort($port === null ? null : (int)(string)$port);

        $query = null;
        if ($serverParams->has('REQUEST_URI')) {
            $requestURI = $serverParams->asString('REQUEST_URI');
            $requestURIParts = explode('?', $requestURI, 2);
            $uri = $uri->withPath(explode('#', $requestURIParts[0])[0]);
            if (isset($requestURIParts[1])) {
                $query = explode('#', $requestURIParts[1])[0];
                $uri = $uri->withQuery($query);
            }

            $requestURIParts = explode('#', $requestURI, 2);
            if (isset($requestURIParts[1])) {
                $uri = $uri->withFragment($requestURIParts[1]);
            }
        }

        if (empty($query) && $serverParams->has('QUERY_STRING')) {
            $uri = $uri->withQuery($serverParams->asString('QUERY_STRING'));
        }
        return $uri;
    }

    /**
     * @return array{
     *   scheme: \Arcanum\Hyper\URI\Scheme,
     *   host: \Arcanum\Hyper\URI\Host,
     *   port: \Arcanum\Hyper\URI\Port,
     *   user: \Arcanum\Hyper\URI\UserInfo,
     *   pass: \Arcanum\Hyper\URI\UserInfo,
     *   path: \Arcanum\Hyper\URI\Path,
     *   query: \Arcanum\Hyper\URI\Query,
     *   fragment: \Arcanum\Hyper\URI\Fragment
     * }
     */
    private static function parse(string $url): array
    {
        /**
         * Handle IPv6 addresses.
         *
         * The callback captures the scheme and the IPv6 address into $prefix,
         * and the rest of the URL into $url.
         */
        $prefix = '';
        if (preg_match('%^(.*://\[[0-9:a-f]+\])(.*?)$%', $url, $matches)) {
            /** @var array{0:string, 1:string, 2:string} $matches */
            $prefix = $matches[1];
            $url = $matches[2];
        }

        /**
         * This RegEx matches any character except:    : / @ ? & = #
         *
         * Pattern and url are treated as UTF-8.
         *
         * The callback function will urlencode the entire string if the regex
         * successfully matches.
         */
        $encoded = preg_replace_callback(
            '%[^:/@?&=#]+%u',
            static fn(array $matches): string => urlencode($matches[0]),
            $url
        );

        $result = parse_url($prefix . $encoded);

        if ($result === false) {
            throw new MalformedURI($url);
        }

        $decoded = array_map(fn(string|int $part) => \urldecode((string)$part), $result);

        $decoded['scheme'] = new Scheme($decoded['scheme'] ?? '');
        $decoded['host'] = new Host($decoded['host'] ?? '');
        $decoded['port'] = new Port($decoded['port'] ?? null);
        $decoded['user'] = new UserInfo($decoded['user'] ?? '');
        $decoded['pass'] = new UserInfo($decoded['pass'] ?? '');
        $decoded['path'] = new Path($decoded['path'] ?? '');
        $decoded['query'] = new Query($decoded['query'] ?? '');
        $decoded['fragment'] = new Fragment($decoded['fragment'] ?? '');

        return $decoded;
    }

    public static function hasDefaultPort(UriInterface $uri): bool
    {
        $scheme = $uri->getScheme();
        if (!isset(Port::DEFAULT_PORTS[$scheme])) {
            return false;
        }
        return Port::DEFAULT_PORTS[$uri->getScheme()] === $uri->getPort();
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
        if (self::hasDefaultPort($this)) {
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

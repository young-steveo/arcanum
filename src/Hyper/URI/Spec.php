<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

use Arcanum\Gather\Registry;
use Psr\Http\Message\UriInterface;

final class Spec
{
    /**
     * Check if a URI has a default port.
     */
    public static function hasDefaultPort(UriInterface $uri): bool
    {
        $scheme = $uri->getScheme();
        if (!isset(Port::DEFAULT_PORTS[$scheme])) {
            return false;
        }
        return Port::DEFAULT_PORTS[$uri->getScheme()] === $uri->getPort();
    }

    /**
     * Create a new URI from server parameters.
     */
    public static function fromServerParams(Registry $serverParams): UriInterface
    {
        $https = $serverParams->asString('HTTPS', 'off');
        $uri = (new URI())->withScheme($https !== 'off' ? 'https' : 'http');
        $uri = static::setPort($uri, $serverParams);
        $uri = static::setHost($uri, $serverParams);
        $uri = static::setPath($uri, $serverParams);
        $uri = static::setQuery($uri, $serverParams);
        return $uri;
    }

    /**
     * Create a copy of $uri with the host and port set from $serverParams.
     */
    protected static function setHost(UriInterface $uri, Registry $serverParams): UriInterface
    {
        if ($serverParams->has('HTTP_HOST')) {
            return static::setHostAndPortFromHttpHost($uri, $serverParams->asString('HTTP_HOST'));
        }

        if ($serverParams->has('SERVER_NAME')) {
            return $uri->withHost($serverParams->asString('SERVER_NAME'));
        }

        if ($serverParams->has('SERVER_ADDR')) {
            return $uri->withHost($serverParams->asString('SERVER_ADDR'));
        }

        return $uri;
    }

    /**
     * Create a copy of $uri with the port set from $serverParams.
     */
    public static function setPort(UriInterface $uri, Registry $serverParams): UriInterface
    {
        if ($serverParams->has('SERVER_PORT')) {
            $uri = $uri->withPort($serverParams->asInt('SERVER_PORT'));
        }
        return $uri;
    }

    /**
     * Create a copy of $uri with the host and port set from $httpHost.
     */
    protected static function setHostAndPortFromHttpHost(UriInterface $uri, string $httpHost): UriInterface
    {
        $authority = Authority::fromAuthorityString($httpHost);
        $host = $authority->getHost();
        $authorityPort = $authority->getPort();
        if ($authorityPort !== null) {
            $uri = $uri->withPort((int)(string)$authorityPort);
        }
        return $uri->withHost((string)$host);
    }

    /**
     * Create a copy of $uri with the path and query set from $serverParams.
     */
    protected static function setPath(UriInterface $uri, Registry $serverParams): UriInterface
    {
        if ($serverParams->has('REQUEST_URI')) {
            $requestURI = $serverParams->asString('REQUEST_URI');

            // match the path, query, and fragment into $parts
            preg_match('/^([^?#]*)(?:\?([^#]*))?(?:#(.*))?$/', $requestURI, $parts);

            list(, $path, $query, $fragment) = $parts;

            return $uri->withPath($path)->withQuery($query)->withFragment($fragment);
        }
        return $uri;
    }

    /**
     * Create a copy of $uri with the query set from $serverParams.
     */
    protected static function setQuery(UriInterface $uri, Registry $serverParams): UriInterface
    {
        if ($serverParams->has('QUERY_STRING')) {
            return $uri->withQuery($serverParams->asString('QUERY_STRING'));
        }
        return $uri;
    }

    /**
     * @return array{
     *   scheme: \Arcanum\Hyper\URI\Scheme,
     *   host: \Arcanum\Hyper\URI\Host,
     *   port: \Arcanum\Hyper\URI\Port|null,
     *   user: \Arcanum\Hyper\URI\UserInfo,
     *   pass: \Arcanum\Hyper\URI\UserInfo,
     *   path: \Arcanum\Hyper\URI\Path,
     *   query: \Arcanum\Hyper\URI\Query,
     *   fragment: \Arcanum\Hyper\URI\Fragment
     * }
     */
    public static function parse(string $url): array
    {
        $parsed = static::parseURL($url);
        $scheme = new Scheme($parsed['scheme'] ?? '');
        $host = new Host($parsed['host'] ?? '');
        $port = isset($parsed['port']) ? new Port($parsed['port']) : null;
        $user = new UserInfo($parsed['user'] ?? '');
        $pass = new UserInfo($parsed['pass'] ?? '');
        $path = new Path($parsed['path'] ?? '');
        $query = new Query($parsed['query'] ?? '');
        $fragment = new Fragment($parsed['fragment'] ?? '');

        return compact('scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment');
    }


    /**
     * @return array{
     *   scheme?: string,
     *   host?: string,
     *   port?: string,
     *   user?: string,
     *   pass?: string,
     *   path?: string,
     *   query?: string,
     *   fragment?: string
     * }
     * @throws \Arcanum\Hyper\URI\MalformedURI
     */
    public static function parseURL(string $url): array
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

        $parts = static::parseFullURL($prefix . $encoded);

        return array_map(fn(string|int $part) => \urldecode((string)$part), $parts);
    }

    /**
     * @return array{
     *  scheme?: string,
     *  host?: string,
     *  port?: int<0, 65535>,
     *  user?: string,
     *  pass?: string,
     *  path?: string,
     *  query?: string,
     *  fragment?: string
     * }
     */
    public static function parseFullURL(string $url): array
    {
        return parse_url($url) ?: throw new MalformedURI($url);
    }
}

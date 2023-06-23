<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

use Arcanum\Gather\Registry;
use Psr\Http\Message\UriInterface;

final class Spec
{
    public static function hasDefaultPort(UriInterface $uri): bool
    {
        $scheme = $uri->getScheme();
        if (!isset(Port::DEFAULT_PORTS[$scheme])) {
            return false;
        }
        return Port::DEFAULT_PORTS[$uri->getScheme()] === $uri->getPort();
    }

    public static function fromServerParams(Registry $serverParams): UriInterface
    {
        $https = $serverParams->asString('HTTPS', 'off');
        $uri = (new URI())->withScheme($https !== 'off' ? 'https' : 'http');

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
    public static function parse(string $url): array
    {
        $parsed = static::parseURL($url);
        $parsed['scheme'] = new Scheme($parsed['scheme'] ?? '');
        $parsed['host'] = new Host($parsed['host'] ?? '');
        $parsed['port'] = new Port($parsed['port'] ?? null);
        $parsed['user'] = new UserInfo($parsed['user'] ?? '');
        $parsed['pass'] = new UserInfo($parsed['pass'] ?? '');
        $parsed['path'] = new Path($parsed['path'] ?? '');
        $parsed['query'] = new Query($parsed['query'] ?? '');
        $parsed['fragment'] = new Fragment($parsed['fragment'] ?? '');

        return $parsed;
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

        $result = parse_url($prefix . $encoded);

        if ($result === false) {
            throw new MalformedURI($url);
        }

        return array_map(fn(string|int $part) => \urldecode((string)$part), $result);
    }
}

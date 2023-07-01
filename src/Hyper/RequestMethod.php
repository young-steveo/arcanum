<?php

declare(strict_types=1);

namespace Arcanum\Hyper;

enum RequestMethod: string
{
    case GET = 'GET';
    case HEAD = 'HEAD';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
    case PATCH = 'PATCH';

    public static function fromString(string $name): static
    {
        return match (strtoupper($name)) {
            'GET' => self::GET,
            'HEAD' => self::HEAD,
            'POST' => self::POST,
            'PUT' => self::PUT,
            'DELETE' => self::DELETE,
            'CONNECT' => self::CONNECT,
            'OPTIONS' => self::OPTIONS,
            'TRACE' => self::TRACE,
            'PATCH' => self::PATCH,
            default => throw new \InvalidArgumentException("Invalid request method: $name"),
        };
    }
}

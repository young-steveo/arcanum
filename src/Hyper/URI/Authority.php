<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

use Psr\Http\Message\UriInterface;

final class Authority implements \Stringable
{
    public function __construct(
        private UserInfo $user,
        private UserInfo $pass,
        private Host $host,
        private Port|null $port = null,
    ) {
    }

    public static function fromAuthorityString(string $authority): self
    {
        $uri = 'http://' . $authority;

        $parts = parse_url($uri);

        return new self(
            new UserInfo($parts['user'] ?? ''),
            new UserInfo($parts['pass'] ?? ''),
            new Host($parts['host'] ?? ''),
            isset($parts['port']) ? new Port($parts['port']) : null,
        );
    }

    public function __toString(): string
    {
        return trim($this->getUserInfo() . '@' . $this->host . ':' . $this->port, '@:');
    }

    public function getUserInfo(): string
    {
        return trim($this->user . ':' . $this->pass, ':');
    }

    public function getHost(): Host
    {
        return $this->host;
    }

    public function getPort(): Port|null
    {
        return $this->port;
    }

    public function withHost(Host $host): self
    {
        return new self($this->user, $this->pass, $host, $this->port);
    }

    public function withPort(Port|null $port): self
    {
        return new self($this->user, $this->pass, $this->host, $port);
    }

    public function withUserInfo(UserInfo $user, UserInfo $pass): self
    {
        return new self($user, $pass, $this->host, $this->port);
    }
}

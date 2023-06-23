<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

use Psr\Http\Message\UriInterface;

final class Authority implements \Stringable
{
    /**
     * Authority
     */
    public function __construct(
        private UserInfo $user,
        private UserInfo $pass,
        private Host $host,
        private Port|null $port = null,
    ) {
    }

    /**
     * Create an authority from a string.
     */
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

    /**
     * Authority as a string.
     */
    public function __toString(): string
    {
        return trim($this->getUserInfo() . '@' . $this->host . ':' . $this->port, '@:');
    }

    /**
     * Get the user info as a string.
     */
    public function getUserInfo(): string
    {
        return trim($this->user . ':' . $this->pass, ':');
    }

    /**
     * Get the Host.
     */
    public function getHost(): Host
    {
        return $this->host;
    }

    /**
     * Get the Port.
     */
    public function getPort(): Port|null
    {
        return $this->port;
    }

    /**
     * Create a copy of the authority with a new host.
     */
    public function withHost(Host $host): self
    {
        return new self($this->user, $this->pass, $host, $this->port);
    }

    /**
     * Create a copy of the authority with a new port.
     */
    public function withPort(Port|null $port): self
    {
        return new self($this->user, $this->pass, $this->host, $port);
    }

    /**
     * Create a copy of the authority with a new user info.
     */
    public function withUserInfo(UserInfo $user, UserInfo $pass): self
    {
        return new self($user, $pass, $this->host, $this->port);
    }
}

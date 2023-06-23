<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\URI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Hyper\URI\Authority;

#[CoversClass(Authority::class)]
#[UsesClass(\Arcanum\Hyper\URI\Host::class)]
#[UsesClass(\Arcanum\Hyper\URI\UserInfo::class)]
#[UsesClass(\Arcanum\Hyper\URI\Port::class)]
final class AuthorityTest extends TestCase
{
    public function testAuthority(): void
    {
        // Arrange
        $authority = Authority::fromAuthorityString('user:pass@example.com:8080');

        // Act
        $data = (string)$authority;

        // Assert
        $this->assertSame('user:pass@example.com:8080', $data);
    }

    public function testAuthorityGetUserInfo(): void
    {
        // Arrange
        $authority = Authority::fromAuthorityString('user:pass@example.com:8080');

        // Act
        $userInfo = $authority->getUserInfo();

        // Assert
        $this->assertSame('user:pass', $userInfo);
    }

    public function testAuthorityGetHost(): void
    {
        // Arrange
        $authority = Authority::fromAuthorityString('user:pass@example.com:8080');

        // Act
        $host = $authority->getHost();

        // Assert
        $this->assertSame('example.com', (string)$host);
    }

    public function testAuthorityGetPort(): void
    {
        // Arrange
        $authority = Authority::fromAuthorityString('user:pass@example.com:8080');

        // Act
        $port = $authority->getPort();

        // Assert
        $this->assertSame('8080', (string)$port);
    }

    public function testWithHost(): void
    {
        // Arrange
        $authority = Authority::fromAuthorityString('user:pass@example.com:8080');

        // Act
        $newAuthority = $authority->withHost(\Arcanum\Hyper\URI\Host::localhost());

        // Assert
        $this->assertSame('user:pass@localhost:8080', (string)$newAuthority);
        $this->assertSame('user:pass@example.com:8080', (string)$authority);
    }

    public function testWithPort(): void
    {
        // Arrange
        $authority = Authority::fromAuthorityString('user:pass@example.com:8080');

        // Act
        $newAuthority = $authority->withPort(new \Arcanum\Hyper\URI\Port(9090));

        // Assert
        $this->assertSame('user:pass@example.com:8080', (string)$authority);
        $this->assertSame('user:pass@example.com:9090', (string)$newAuthority);
    }

    public function testWithUserInfo(): void
    {
        // Arrange
        $authority = Authority::fromAuthorityString('user:pass@example.com:8080');

        // Act
        $newAuthority = $authority->withUserInfo(
            new \Arcanum\Hyper\URI\UserInfo('username'),
            new \Arcanum\Hyper\URI\UserInfo('password')
        );

        // Assert
        $this->assertSame('user:pass@example.com:8080', (string)$authority);
        $this->assertSame('username:password@example.com:8080', (string)$newAuthority);
    }
}

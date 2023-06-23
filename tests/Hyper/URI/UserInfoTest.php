<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\URI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Hyper\URI\UserInfo;

#[CoversClass(UserInfo::class)]
final class UserInfoTest extends TestCase
{
    public function testUserInfo(): void
    {
        // Arrange
        $userInfo = new UserInfo('username');

        // Act
        $username = (string)$userInfo;

        // Assert
        $this->assertSame('username', $username);
    }

    public function testUserInfoEncodesNonURLCharacters(): void
    {
        // Arrange
        $userInfo = new UserInfo('user:pass');

        // Act
        $data = (string)$userInfo;

        // Assert
        $this->assertSame('user%3Apass', $data);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper;

use Arcanum\Hyper\Version;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Version::class)]
final class VersionTest extends TestCase
{
    public function testVersion(): void
    {
        $this->assertSame('0.9', Version::v09->value);
        $this->assertSame('1.0', Version::v10->value);
        $this->assertSame('1.1', Version::v11->value);
        $this->assertSame('2.0', Version::v20->value);
    }

    public function testFromString(): void
    {
        $this->assertSame(Version::v09, Version::fromString('0.9'));
        $this->assertSame(Version::v10, Version::fromString('1.0'));
        $this->assertSame(Version::v11, Version::fromString('1.1'));
        $this->assertSame(Version::v20, Version::fromString('2.0'));
    }

    public function testFromStringThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP version');
        Version::fromString('FOO');
    }
}

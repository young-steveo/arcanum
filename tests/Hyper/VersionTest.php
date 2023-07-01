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

    public function testFrom(): void
    {
        $this->assertSame(Version::v09, Version::from('0.9'));
        $this->assertSame(Version::v10, Version::from('1.0'));
        $this->assertSame(Version::v11, Version::from('1.1'));
        $this->assertSame(Version::v20, Version::from('2.0'));
    }
}

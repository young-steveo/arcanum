<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\URI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Hyper\URI\Fragment;

#[CoversClass(Fragment::class)]
final class FragmentTest extends TestCase
{
    public function testFragment(): void
    {
        // Arrange
        $query = new Fragment('#id');

        // Act
        $data = (string)$query;

        // Assert
        $this->assertSame('%23id', $data);
    }

    public function testFragmentEncodesNonURLCharacters(): void
    {
        // Arrange
        $query = new Fragment('#id&bar=b az');

        // Act
        $data = (string)$query;

        // Assert
        $this->assertSame('%23id&bar=b%20az', $data);
    }
}

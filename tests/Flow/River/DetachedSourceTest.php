<?php

declare(strict_types=1);

namespace Arcanum\Flow\River;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Flow\River\DetachedSource::class)]
final class DetachedSourceTest extends TestCase
{
    public function testDetachedSource(): void
    {
        // Arrange
        $invalidKey = new \Arcanum\Flow\River\DetachedSource('foo');

        // Act
        $message = $invalidKey->getMessage();

        // Assert
        $this->assertEquals('Detached source: foo', $message);
    }
}

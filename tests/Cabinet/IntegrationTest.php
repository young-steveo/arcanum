<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Cabinet\Container;

/**
 * Tests that require the full container.
 */
#[CoversClass(Container::class)]
#[UsesClass(\Arcanum\Codex\Resolver::class)]
final class IntegrationTest extends TestCase
{
    public function testCreateContainer(): void
    {
        // Assert
        $this->assertInstanceOf(Container::class, Container::create());
    }
}

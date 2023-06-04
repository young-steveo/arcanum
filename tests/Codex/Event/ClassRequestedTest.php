<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Event;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Codex\Event\ClassRequested;

#[CoversClass(ClassRequested::class)]
final class ClassRequestedTest extends TestCase
{
    public function testClassRequested(): void
    {
        // Arrange
        $event = new ClassRequested(get_class(new \stdClass()));

        // Act
        $class = $event->Class();
        $name = $event->ClassName();

        // Assert
        $this->assertNull($class);
        $this->assertSame('stdClass', $name);
    }
}

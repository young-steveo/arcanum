<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Event;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Codex\Event\ClassResolved;

#[CoversClass(ClassResolved::class)]
final class ClassResolvedTest extends TestCase
{
    public function testClassResolved(): void
    {
        // Arrange
        $event = new ClassResolved(new \stdClass());

        // Act
        $class = $event->Class();
        $name = $event->ClassName();

        // Assert
        $this->assertInstanceOf(\stdClass::class, $class);
        $this->assertSame('stdClass', $name);
    }
}

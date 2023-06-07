<?php

declare(strict_types=1);

namespace Arcanum\Test\Conveyor\Middleware;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Conveyor\Middleware\ReadOnlyPropertyFilter;
use Arcanum\Conveyor\Middleware\InvalidDTO;
use Arcanum\Test\Conveyor\Fixture\ValidDTO;
use Arcanum\Test\Conveyor\Fixture\HasMutableProperty;

#[CoversClass(ReadOnlyPropertyFilter::class)]
#[UsesClass(InvalidDTO::class)]
final class ReadOnlyPropertyFilterTest extends TestCase
{
    public function testReadOnlyPropertyFilter(): void
    {
        // Arrange
        $filter = new ReadOnlyPropertyFilter();

        // Act
        $filter(ValidDTO::fromName("test"), fn() => $this->expectNotToPerformAssertions());
    }

    public function testReadOnlyPropertyFilterThrowsInvalidDTO(): void
    {
        // Arrange
        $filter = new ReadOnlyPropertyFilter();

        // Assert
        $this->expectException(InvalidDTO::class);
        $this->expectExceptionMessage(
            "Invalid DTO: Arcanum\Test\Conveyor\Fixture\HasMutableProperty has properties that are not readonly."
        );

        // Act
        $filter(
            new HasMutableProperty("test"),
            fn() => $this->fail("Filter allowed class with mutable properties to slip through.")
        );
    }
}

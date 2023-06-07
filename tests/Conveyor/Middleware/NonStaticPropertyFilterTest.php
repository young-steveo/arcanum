<?php

declare(strict_types=1);

namespace Arcanum\Test\Conveyor\Middleware;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Conveyor\Middleware\NonStaticPropertyFilter;
use Arcanum\Conveyor\Middleware\InvalidDTO;
use Arcanum\Test\Conveyor\Fixture\ValidDTO;
use Arcanum\Test\Conveyor\Fixture\HasStaticProperty;

#[CoversClass(NonStaticPropertyFilter::class)]
#[UsesClass(InvalidDTO::class)]
final class NonStaticPropertyFilterTest extends TestCase
{
    public function testNonStaticPropertyFilter(): void
    {
        // Arrange
        $filter = new NonStaticPropertyFilter();

        // Act
        $filter(ValidDTO::fromName("test"), fn() => $this->expectNotToPerformAssertions());
    }

    public function testNonStaticPropertyFilterThrowsInvalidDTO(): void
    {
        // Arrange
        $filter = new NonStaticPropertyFilter();

        // Assert
        $this->expectException(InvalidDTO::class);
        $this->expectExceptionMessage(
            "Invalid DTO: Arcanum\Test\Conveyor\Fixture\HasStaticProperty has static properties."
        );

        // Act
        $filter(
            new HasStaticProperty(),
            fn() => $this->fail("Filter allowed class with static properties to slip through.")
        );
    }
}

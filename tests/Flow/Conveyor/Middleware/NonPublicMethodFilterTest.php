<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Conveyor\Middleware;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\Conveyor\Middleware\NonPublicMethodFilter;
use Arcanum\Flow\Conveyor\Middleware\InvalidDTO;
use Arcanum\Test\Flow\Conveyor\Fixture\ValidDTO;
use Arcanum\Test\Flow\Conveyor\Fixture\HasPublicMethod;

#[CoversClass(NonPublicMethodFilter::class)]
#[UsesClass(InvalidDTO::class)]
final class NonPublicMethodFilterTest extends TestCase
{
    public function testNonPublicMethodFilter(): void
    {
        // Arrange
        $filter = new NonPublicMethodFilter();

        // Act
        $filter(ValidDTO::fromName("test"), fn() => $this->expectNotToPerformAssertions());
    }

    public function testNonPublicMethodFilterThrowsInvalidDTO(): void
    {
        // Arrange
        $filter = new NonPublicMethodFilter();

        // Assert
        $this->expectException(InvalidDTO::class);
        $this->expectExceptionMessage(
            "Invalid DTO: Arcanum\Test\Flow\Conveyor\Fixture\HasPublicMethod has non-static methods that are public."
        );

        // Act
        $filter(
            new HasPublicMethod(),
            fn() => $this->fail("Filter allowed class with public non-static methods to slip through.")
        );
    }
}

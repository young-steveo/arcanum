<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Conveyor\Middleware;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\Conveyor\Middleware\PublicPropertyFilter;
use Arcanum\Flow\Conveyor\Middleware\InvalidDTO;
use Arcanum\Test\Flow\Conveyor\Fixture\ValidDTO;
use Arcanum\Test\Flow\Conveyor\Fixture\HasProtectedProperty;

#[CoversClass(PublicPropertyFilter::class)]
#[UsesClass(InvalidDTO::class)]
final class PublicPropertyFilterTest extends TestCase
{
    public function testPublicPropertyFilter(): void
    {
        // Arrange
        $filter = new PublicPropertyFilter();

        // Act
        $filter(ValidDTO::fromName("test"), fn() => $this->expectNotToPerformAssertions());
    }

    public function testPublicPropertyFilterThrowsInvalidDTO(): void
    {
        // Arrange
        $filter = new PublicPropertyFilter();

        // Assert
        $this->expectException(InvalidDTO::class);
        $this->expectExceptionMessage(
            "Invalid DTO: Arcanum\Test\Flow\Conveyor\Fixture\HasProtectedProperty has private or protected properties."
        );

        // Act
        $filter(
            new HasProtectedProperty(),
            fn() => $this->fail("Filter allowed class with non-public properties to slip through.")
        );
    }
}

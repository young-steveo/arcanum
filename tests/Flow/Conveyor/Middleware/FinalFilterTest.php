<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Conveyor\Middleware;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\Conveyor\Middleware\FinalFilter;
use Arcanum\Flow\Conveyor\Middleware\InvalidDTO;
use Arcanum\Test\Flow\Conveyor\Fixture\ValidDTO;

#[CoversClass(FinalFilter::class)]
#[UsesClass(InvalidDTO::class)]
final class FinalFilterTest extends TestCase
{
    public function testFinalFilter(): void
    {
        // Arrange
        $filter = new FinalFilter();

        // Act
        $filter(ValidDTO::fromName("test"), fn() => $this->expectNotToPerformAssertions());
    }

    public function testFinalFilterThrowsInvalidDTO(): void
    {
        // Arrange
        $filter = new FinalFilter();

        // Assert
        $this->expectException(InvalidDTO::class);
        $this->expectExceptionMessage("Invalid DTO: stdClass is not final.");

        // Act
        $filter(new \stdClass(), fn() => $this->fail("Filter allowed non-final class to slip through."));
    }
}

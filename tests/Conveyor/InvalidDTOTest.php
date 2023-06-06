<?php

declare(strict_types=1);

namespace Arcanum\Test\Conveyor;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Conveyor\InvalidDTO::class)]
final class InvalidDTOTest extends TestCase
{
    public function testInvalidDTO(): void
    {
        // Arrange
        $InvalidDTO = new \Arcanum\Conveyor\InvalidDTO('foo');

        // Act
        $message = $InvalidDTO->getMessage();

        // Assert
        $this->assertEquals('Invalid DTO: foo', $message);
    }
}

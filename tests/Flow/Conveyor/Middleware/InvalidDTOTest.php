<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Conveyor\Middleware;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Flow\Conveyor\Middleware\InvalidDTO::class)]
final class InvalidDTOTest extends TestCase
{
    public function testInvalidDTO(): void
    {
        // Arrange
        $InvalidDTO = new \Arcanum\Flow\Conveyor\Middleware\InvalidDTO('foo');

        // Act
        $message = $InvalidDTO->getMessage();

        // Assert
        $this->assertEquals('Invalid DTO: foo', $message);
    }
}

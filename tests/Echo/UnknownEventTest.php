<?php

declare(strict_types=1);

namespace Arcanum\Test\Echo;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Echo\UnknownEvent::class)]
final class UnknownEventTest extends TestCase
{
    public function testFromObjectNameAndPayload(): void
    {
        // Arrange
        $object = new \stdClass();

        // Act
        $result = \Arcanum\Echo\UnknownEvent::fromObject($object);

        // Assert
        $this->assertInstanceOf(\Arcanum\Echo\UnknownEvent::class, $result);
        $this->assertSame("stdClass", $result->name);
        $this->assertSame($object, $result->payload);
    }
}

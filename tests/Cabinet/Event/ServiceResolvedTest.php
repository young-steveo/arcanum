<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Event;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Cabinet\Event\ServiceResolved;

#[CoversClass(ServiceResolved::class)]
final class ServiceResolvedTest extends TestCase
{
    public function testServiceResolved(): void
    {
        // Arrange
        $event = new ServiceResolved(
            service: new \stdClass(),
        );

        // Act
        $service = $event->service();

        // Assert
        $this->assertInstanceOf(\stdClass::class, $service);
    }
}

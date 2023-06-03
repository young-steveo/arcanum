<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Event;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Cabinet\Event\ServiceRequested;

#[CoversClass(ServiceRequested::class)]
final class ServiceRequestedTest extends TestCase
{
    public function testServiceRequested(): void
    {
        // Arrange
        $event = new ServiceRequested(
            get_class(new \stdClass()),
        );

        // Act
        $service = $event->service();
        $name = $event->serviceName();

        // Assert
        $this->assertNull($service);
        $this->assertSame('stdClass', $name);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Test\Cabinet\Stub;
use Arcanum\Cabinet\Container;

/**
 * Tests that require the full container.
 */
#[CoversClass(Container::class)]
#[UsesClass(\Arcanum\Cabinet\Resolver::class)]
final class IntegrationTest extends TestCase
{
    public function testCreateContainer(): void
    {
        // Assert
        $this->assertInstanceOf(Container::class, Container::create());
    }

    #[CoversNothing]
    public function testContainerService(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Stub\SimpleService::class);

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Stub\SimpleService::class, $result);
        $this->assertSame($container->get(Stub\SimpleService::class), $result);
    }

    #[CoversNothing]
    public function testResolveDependencies(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Stub\SimpleService::class);
        $container->service(Stub\SimpleDependency::class);

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Stub\SimpleService::class, $result);
    }

    #[CoversNothing]
    public function testResolveDependenciesNotRegisteredButFindable(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Stub\SimpleService::class);

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Stub\SimpleService::class, $result);
    }

    #[CoversNothing]
    public function testPrototype(): void
    {
        // Arrange
        $container = Container::create();
        $container->prototype(Stub\SimpleService::class);

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Stub\SimpleService::class, $result);
        $this->assertNotSame($container->get(Stub\SimpleService::class), $result);
    }

    #[CoversNothing]
    public function testResolveRegisteredInterface(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Stub\ServiceWithInterface::class);
        $container->service(Stub\ServiceInterface::class, Stub\ConcreteService::class);

        // Act
        $result = $container->get(Stub\ServiceWithInterface::class);

        // Assert
        $this->assertInstanceOf(Stub\ServiceWithInterface::class, $result);
        $this->assertInstanceOf(Stub\ConcreteService::class, $result->dependency);
    }
}

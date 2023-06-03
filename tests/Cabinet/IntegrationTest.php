<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Test\Cabinet\Fixture;
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
        $container->service(Fixture\SimpleService::class);

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleService::class, $result);
        $this->assertSame($container->get(Fixture\SimpleService::class), $result);
    }

    #[CoversNothing]
    public function testResolveDependencies(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Fixture\SimpleService::class);
        $container->service(Fixture\SimpleDependency::class);

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleService::class, $result);
    }

    #[CoversNothing]
    public function testResolveDependenciesNotRegisteredButFindable(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Fixture\SimpleService::class);

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleService::class, $result);
    }

    #[CoversNothing]
    public function testPrototype(): void
    {
        // Arrange
        $container = Container::create();
        $container->prototype(Fixture\SimpleService::class);

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleService::class, $result);
        $this->assertNotSame($container->get(Fixture\SimpleService::class), $result);
    }

    #[CoversNothing]
    public function testResolveRegisteredInterface(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Fixture\ServiceWithInterface::class);
        $container->service(Fixture\ServiceInterface::class, Fixture\ConcreteService::class);

        // Act
        $result = $container->get(Fixture\ServiceWithInterface::class);

        // Assert
        $this->assertInstanceOf(Fixture\ServiceWithInterface::class, $result);
        $this->assertInstanceOf(Fixture\ConcreteService::class, $result->dependency);
    }
}

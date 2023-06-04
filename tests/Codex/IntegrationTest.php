<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Test\Codex\Fixture;
use Arcanum\Cabinet\Container;

/**
 * Tests that require the full container.
 */
#[CoversClass(Container::class)]
#[UsesClass(\Arcanum\Codex\Resolver::class)]
final class IntegrationTest extends TestCase
{
    #[CoversNothing]
    public function testContainerService(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Fixture\SimpleClass::class);

        // Act
        $result = $container->get(Fixture\SimpleClass::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleClass::class, $result);
        $this->assertSame($container->get(Fixture\SimpleClass::class), $result);
    }

    #[CoversNothing]
    public function testResolveDependencies(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Fixture\SimpleClass::class);
        $container->service(Fixture\SimpleDependency::class);

        // Act
        $result = $container->get(Fixture\SimpleClass::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleClass::class, $result);
    }

    #[CoversNothing]
    public function testResolveDependenciesNotRegisteredButFindable(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Fixture\SimpleClass::class);

        // Act
        $result = $container->get(Fixture\SimpleClass::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleClass::class, $result);
    }

    #[CoversNothing]
    public function testPrototype(): void
    {
        // Arrange
        $container = Container::create();
        $container->prototype(Fixture\SimpleClass::class);

        // Act
        $result = $container->get(Fixture\SimpleClass::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleClass::class, $result);
        $this->assertNotSame($container->get(Fixture\SimpleClass::class), $result);
    }

    #[CoversNothing]
    public function testResolveRegisteredInterface(): void
    {
        // Arrange
        $container = Container::create();
        $container->service(Fixture\ServiceWithInterface::class);
        $container->service(Fixture\ServiceInterface::class, Fixture\ConcreteClass::class);

        // Act
        $result = $container->get(Fixture\ServiceWithInterface::class);

        // Assert
        $this->assertInstanceOf(Fixture\ServiceWithInterface::class, $result);
        $this->assertInstanceOf(Fixture\ConcreteClass::class, $result->dependency);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Test\Cabinet\Stub\SimpleService;
use Arcanum\Test\Cabinet\Stub\SimpleDependency;

#[CoversClass(\Arcanum\Cabinet\Container::class)]
#[UsesClass(\Arcanum\Cabinet\Resolver::class)]
#[UsesClass(\Arcanum\Cabinet\SimpleProvider::class)]
#[UsesClass(\Arcanum\Cabinet\Error\OutOfBounds::class)]
#[UsesClass(\Arcanum\Cabinet\Error\InvalidKey::class)]
final class ContainerTest extends TestCase
{
    public function testContainerImplementsArrayAccess(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $service = new SimpleService(new SimpleDependency());

        // Act
        $container[SimpleService::class] = $service;

        // Assert
        $this->assertSame($service, $container[SimpleService::class]);
    }

    public function testContainerThrowsOutOfBoundsIfArrayAccessOffsetDoesNotExist(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();

        // Assert
        $this->expectException(\Arcanum\Cabinet\Error\OutOfBounds::class);

        // Act
        $container[SimpleService::class]; /** @phpstan-ignore-line */
    }

    public function testContainerOffsetUnset(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $container[SimpleService::class] = new SimpleService(new SimpleDependency());

        // Act
        unset($container[SimpleService::class]);

        // Assert
        $this->assertFalse(isset($container[SimpleService::class]));
    }

    public function testContainerOnlyAcceptsStringKeys(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();

        // Assert
        $this->expectException(\Arcanum\Cabinet\Error\InvalidKey::class);

        // Act
        $container[0] = 'bar'; /** @phpstan-ignore-line */
    }

    public function testContainerCannotAccessNonStringKey(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();

        // Assert
        $this->expectException(\Arcanum\Cabinet\Error\InvalidKey::class);

        // Act
        $container[0]; /** @phpstan-ignore-line */
    }

    public function testContainerGetService(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $service = new SimpleService(new SimpleDependency());
        $container[SimpleService::class] = $service;

        // Act
        $result = $container->get(SimpleService::class);

        // Assert
        $this->assertSame($service, $result);
    }

    public function testContainerGetThrowsOutOfBoundsIfServiceDoesNotExist(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();

        // Assert
        $this->expectException(\Arcanum\Cabinet\Error\OutOfBounds::class);

        // Act
        $container->get(SimpleService::class);
    }

    public function testContainerHas(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $container[SimpleService::class] = new SimpleService(new SimpleDependency());

        // Act
        $result = $container->has(SimpleService::class);

        // Assert
        $this->assertTrue($result);
    }

    public function testContainerProvider(): void
    {
        // Arrange

        $service = new SimpleService(new SimpleDependency());

        /** @var \Arcanum\Cabinet\Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(\Arcanum\Cabinet\Provider::class)
            ->onlyMethods(['__invoke'])
            ->getMock();

        $provider->expects($this->once())
            ->method('__invoke')
            ->willReturn($service);

        $container = new \Arcanum\Cabinet\Container();

        // Act
        $container->provider(SimpleService::class, $provider);
        $result = $container->get(SimpleService::class);

        // Assert
        $this->assertSame($service, $result);
    }

    public function testContainerFactory(): void
    {
        // Arrange
        $service = new SimpleService(new SimpleDependency());
        $container = new \Arcanum\Cabinet\Container();
        $container->factory(SimpleService::class, fn() => $service);

        // Act
        $result = $container->get(SimpleService::class);

        // Assert
        $this->assertSame($service, $result);
    }

    public function testContainerService(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $container->service(SimpleService::class);

        // Act
        $result = $container->get(SimpleService::class);

        // Assert
        $this->assertInstanceOf(SimpleService::class, $result);
        $this->assertSame($container->get(SimpleService::class), $result);
    }

    public function testResolveDependencies(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $container->service(SimpleService::class);
        $container->service(SimpleDependency::class);

        // Act
        $result = $container->get(SimpleService::class);

        // Assert
        $this->assertInstanceOf(SimpleService::class, $result);
    }

    public function testResolveDependenciesNotRegisteredButFindable(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $container->service(SimpleService::class);

        // Act
        $result = $container->get(SimpleService::class);

        // Assert
        $this->assertInstanceOf(SimpleService::class, $result);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Test\Cabinet\Fixture;
use Arcanum\Cabinet\Error;
use Arcanum\Cabinet\Container;

#[CoversClass(Container::class)]
#[UsesClass(\Arcanum\Cabinet\SimpleProvider::class)]
#[UsesClass(\Arcanum\Cabinet\PrototypeProvider::class)]
#[UsesClass(\Arcanum\Cabinet\NullProvider::class)]
#[UsesClass(Error\OutOfBounds::class)]
#[UsesClass(Error\InvalidKey::class)]
final class ContainerTest extends TestCase
{
    public function testContainerImplementsArrayAccess(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        $service = new Fixture\SimpleService(new Fixture\SimpleDependency());

        // Act
        $container[Fixture\SimpleService::class] = $service;

        // Assert
        $this->assertSame($service, $container[Fixture\SimpleService::class]);
    }

    public function testContainerThrowsOutOfBoundsIfArrayAccessOffsetDoesNotExist(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        // Assert
        $this->expectException(Error\OutOfBounds::class);

        // Act
        $container[Fixture\SimpleService::class]; /** @phpstan-ignore-line */
    }

    public function testContainerOffsetUnset(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);
        $container[Fixture\SimpleService::class] = new Fixture\SimpleService(new Fixture\SimpleDependency());

        // Act
        unset($container[Fixture\SimpleService::class]);

        // Assert
        $this->assertFalse(isset($container[Fixture\SimpleService::class]));
    }

    public function testContainerOnlyAcceptsStringKeys(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        // Assert
        $this->expectException(Error\InvalidKey::class);

        // Act
        $container[0] = 'bar'; /** @phpstan-ignore-line */
    }

    public function testContainerCannotAccessNonStringKey(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        // Assert
        $this->expectException(Error\InvalidKey::class);

        // Act
        $container[0]; /** @phpstan-ignore-line */
    }

    public function testContainerGetService(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);
        $service = new Fixture\SimpleService(new Fixture\SimpleDependency());
        $container[Fixture\SimpleService::class] = $service;

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertSame($service, $result);
    }

    public function testContainerGetThrowsOutOfBoundsIfServiceDoesNotExist(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        // Assert
        $this->expectException(Error\OutOfBounds::class);

        // Act
        $container->get(Fixture\SimpleService::class);
    }

    public function testContainerHas(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);
        $container[Fixture\SimpleService::class] = new Fixture\SimpleService(new Fixture\SimpleDependency());

        // Act
        $result = $container->has(Fixture\SimpleService::class);

        // Assert
        $this->assertTrue($result);
    }

    public function testContainerProvider(): void
    {
        // Arrange

        $service = new Fixture\SimpleService(new Fixture\SimpleDependency());

        /** @var \Arcanum\Cabinet\Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(\Arcanum\Cabinet\Provider::class)
            ->onlyMethods(['__invoke'])
            ->getMock();

        $provider->expects($this->once())
            ->method('__invoke')
            ->willReturn($service);

        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        // Act
        $container->provider(Fixture\SimpleService::class, $provider);
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertSame($service, $result);
    }

    public function testContainerFactory(): void
    {
        // Arrange
        $service = new Fixture\SimpleService(new Fixture\SimpleDependency());

        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        // Act
        $container->factory(Fixture\SimpleService::class, fn() => $service);
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertSame($service, $result);
    }

    public function testContainerService(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(Fixture\SimpleService::class)
            ->willReturn(new Fixture\SimpleService(new Fixture\SimpleDependency()));

        $container = Container::fromResolver($resolver);
        $container->service(Fixture\SimpleService::class);

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleService::class, $result);
        $this->assertSame($container->get(Fixture\SimpleService::class), $result);
    }

    public function testResolveDependencies(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(Fixture\SimpleService::class)
            ->willReturn(new Fixture\SimpleService(new Fixture\SimpleDependency()));

        $container = Container::fromResolver($resolver);
        $container->service(Fixture\SimpleService::class);
        $container->service(Fixture\SimpleDependency::class);

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleService::class, $result);
    }

    public function testResolveDependenciesNotRegisteredButFindable(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(Fixture\SimpleService::class)
            ->willReturn(new Fixture\SimpleService(new Fixture\SimpleDependency()));

        $container = Container::fromResolver($resolver);
        $container->service(Fixture\SimpleService::class);

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleService::class, $result);
    }

    public function testRegisterAnInstance(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);
        $instance = new Fixture\SimpleService(new Fixture\SimpleDependency());
        $container->instance(Fixture\SimpleService::class, $instance);

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertSame($instance, $result);
    }

    public function testPrototype(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->exactly(2))
            ->method('resolve')
            ->with(Fixture\SimpleService::class)
            ->willReturnCallback(function () {
                return new Fixture\SimpleService(new Fixture\SimpleDependency());
            });

        $container = Container::fromResolver($resolver);
        $container->prototype(Fixture\SimpleService::class);

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleService::class, $result);
        $this->assertNotSame($container->get(Fixture\SimpleService::class), $result);
    }

    public function testPrototypeFactory(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);
        $container->prototypeFactory(
            serviceName: Fixture\SimpleService::class,
            factory: fn() => new Fixture\SimpleService(new Fixture\SimpleDependency())
        );

        // Act
        $result = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleService::class, $result);
        $this->assertNotSame($container->get(Fixture\SimpleService::class), $result);
    }

    public function testRegisterConcreteImplementationOfInterface(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(Fixture\ConcreteService::class)
            ->willReturnCallback(fn() => new Fixture\ConcreteService());

        $container = Container::fromResolver($resolver);
        $container->service(Fixture\ServiceInterface::class, Fixture\ConcreteService::class);

        // Act
        $result = $container->get(Fixture\ServiceInterface::class);

        // Assert
        $this->assertInstanceOf(Fixture\ConcreteService::class, $result);
    }
}

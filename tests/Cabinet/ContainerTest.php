<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Test\Cabinet\Stub;
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
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        $service = new Stub\SimpleService(new Stub\SimpleDependency());

        // Act
        $container[Stub\SimpleService::class] = $service;

        // Assert
        $this->assertSame($service, $container[Stub\SimpleService::class]);
    }

    public function testContainerThrowsOutOfBoundsIfArrayAccessOffsetDoesNotExist(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        // Assert
        $this->expectException(Error\OutOfBounds::class);

        // Act
        $container[Stub\SimpleService::class]; /** @phpstan-ignore-line */
    }

    public function testContainerOffsetUnset(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);
        $container[Stub\SimpleService::class] = new Stub\SimpleService(new Stub\SimpleDependency());

        // Act
        unset($container[Stub\SimpleService::class]);

        // Assert
        $this->assertFalse(isset($container[Stub\SimpleService::class]));
    }

    public function testContainerOnlyAcceptsStringKeys(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
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
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
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
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);
        $service = new Stub\SimpleService(new Stub\SimpleDependency());
        $container[Stub\SimpleService::class] = $service;

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertSame($service, $result);
    }

    public function testContainerGetThrowsOutOfBoundsIfServiceDoesNotExist(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        // Assert
        $this->expectException(Error\OutOfBounds::class);

        // Act
        $container->get(Stub\SimpleService::class);
    }

    public function testContainerHas(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);
        $container[Stub\SimpleService::class] = new Stub\SimpleService(new Stub\SimpleDependency());

        // Act
        $result = $container->has(Stub\SimpleService::class);

        // Assert
        $this->assertTrue($result);
    }

    public function testContainerProvider(): void
    {
        // Arrange

        $service = new Stub\SimpleService(new Stub\SimpleDependency());

        /** @var \Arcanum\Cabinet\Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(\Arcanum\Cabinet\Provider::class)
            ->onlyMethods(['__invoke'])
            ->getMock();

        $provider->expects($this->once())
            ->method('__invoke')
            ->willReturn($service);

        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        // Act
        $container->provider(Stub\SimpleService::class, $provider);
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertSame($service, $result);
    }

    public function testContainerFactory(): void
    {
        // Arrange
        $service = new Stub\SimpleService(new Stub\SimpleDependency());

        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);

        // Act
        $container->factory(Stub\SimpleService::class, fn() => $service);
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertSame($service, $result);
    }

    public function testContainerService(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(Stub\SimpleService::class)
            ->willReturn(new Stub\SimpleService(new Stub\SimpleDependency()));

        $container = Container::fromResolver($resolver);
        $container->service(Stub\SimpleService::class);

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Stub\SimpleService::class, $result);
        $this->assertSame($container->get(Stub\SimpleService::class), $result);
    }

    public function testResolveDependencies(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(Stub\SimpleService::class)
            ->willReturn(new Stub\SimpleService(new Stub\SimpleDependency()));

        $container = Container::fromResolver($resolver);
        $container->service(Stub\SimpleService::class);
        $container->service(Stub\SimpleDependency::class);

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Stub\SimpleService::class, $result);
    }

    public function testResolveDependenciesNotRegisteredButFindable(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(Stub\SimpleService::class)
            ->willReturn(new Stub\SimpleService(new Stub\SimpleDependency()));

        $container = Container::fromResolver($resolver);
        $container->service(Stub\SimpleService::class);

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Stub\SimpleService::class, $result);
    }

    public function testRegisterAnInstance(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);
        $instance = new Stub\SimpleService(new Stub\SimpleDependency());
        $container->instance(Stub\SimpleService::class, $instance);

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertSame($instance, $result);
    }

    public function testPrototype(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->exactly(2))
            ->method('resolve')
            ->with(Stub\SimpleService::class)
            ->willReturnCallback(function () {
                return new Stub\SimpleService(new Stub\SimpleDependency());
            });

        $container = Container::fromResolver($resolver);
        $container->prototype(Stub\SimpleService::class);

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Stub\SimpleService::class, $result);
        $this->assertNotSame($container->get(Stub\SimpleService::class), $result);
    }

    public function testPrototypeFactory(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->never())
            ->method('resolve');

        $container = Container::fromResolver($resolver);
        $container->prototypeFactory(
            serviceName: Stub\SimpleService::class,
            factory: fn() => new Stub\SimpleService(new Stub\SimpleDependency())
        );

        // Act
        $result = $container->get(Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Stub\SimpleService::class, $result);
        $this->assertNotSame($container->get(Stub\SimpleService::class), $result);
    }

    public function testRegisterConcreteImplementationOfInterface(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Resolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Cabinet\Resolver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['resolve'])
            ->getMock();

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(Stub\ConcreteService::class)
            ->willReturnCallback(fn() => new Stub\ConcreteService());

        $container = Container::fromResolver($resolver);
        $container->service(Stub\ServiceInterface::class, Stub\ConcreteService::class);

        // Act
        $result = $container->get(Stub\ServiceInterface::class);

        // Assert
        $this->assertInstanceOf(Stub\ConcreteService::class, $result);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Test\Cabinet\Fixture;
use Arcanum\Cabinet\Error;
use Arcanum\Cabinet\Resolver;
use Arcanum\Cabinet\Container;
use Arcanum\Cabinet\EventDispatcher;
use Arcanum\Cabinet\Event\CabinetEvent;
use Arcanum\Cabinet\Event\ServiceResolved;
use Arcanum\Cabinet\Event\ServiceRequested;
use Arcanum\Test\Cabinet\Fixture\SimpleDependency;

#[CoversClass(Resolver::class)]
#[UsesClass(Error\UnresolvableClass::class)]
#[UsesClass(Error\UnresolvablePrimitive::class)]
#[UsesClass(Error\UnresolvableUnionType::class)]
#[UsesClass(ServiceResolved::class)]
#[UsesClass(ServiceRequested::class)]
final class ResolverTest extends TestCase
{
    public function testClosure(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->never())
            ->method('has');

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(fn (): object => new \stdClass());

        // Assert
        $this->assertInstanceOf(\stdClass::class, $resolved);
    }

    public function testClassWithNoConstructor(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->once())
            ->method('has')
            ->with(Fixture\SimpleDependency::class)
            ->willReturn(false);

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Fixture\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleService::class, $resolved);
    }

    public function testClassWithConstructorThatHasNoParameters(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->never())
            ->method('has');

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Fixture\ConcreteService::class);

        // Assert
        $this->assertInstanceOf(Fixture\ConcreteService::class, $resolved);
    }

    public function testClassWithDefaultPrimitives(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->never())
            ->method('has');

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Fixture\PrimitiveService::class);

        // Assert
        $this->assertInstanceOf(Fixture\PrimitiveService::class, $resolved);
        $this->assertSame("", $resolved->getString());
        $this->assertSame(0, $resolved->getInt());
        $this->assertSame(0.0, $resolved->getFloat());
        $this->assertSame(false, $resolved->getBool());
        $this->assertSame([], $resolved->getArray());
        $this->assertInstanceOf(\stdClass::class, $resolved->getObject());
        $this->assertInstanceOf(Fixture\SimpleDependency::class, $resolved->getMixed());
        $this->assertNull($resolved->getNull());
    }

    public function testVariadicPrimitives(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->never())
            ->method('has');

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Fixture\VariadicPrimitiveService::class);

        // Assert
        $this->assertInstanceOf(Fixture\VariadicPrimitiveService::class, $resolved);
        $this->assertSame([], $resolved->strings);
    }

    public function testDefaultValuesWithNoTypeHint(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->never())
            ->method('has');

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Fixture\DefaultPrimitiveServiceWithNoType::class);

        // Assert
        $this->assertInstanceOf(Fixture\DefaultPrimitiveServiceWithNoType::class, $resolved);
        $this->assertSame(0, $resolved->test);
    }

    public function testParentKeywordInDependencies(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->once())
            ->method('has')
            ->with(Fixture\SimpleDependency::class)
            ->willReturn(false);

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Fixture\ParentService::class);

        // Assert
        $this->assertInstanceOf(Fixture\ParentService::class, $resolved);
        $this->assertInstanceOf(Fixture\SimpleDependency::class, $resolved->dependency);
    }

    public function testAnUninstantiableClass(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->never())
            ->method('has');

        $resolver = Resolver::forContainer($container);

        // Assert
        $this->expectException(Error\UnresolvableClass::class);

        // Act
        $resolver->resolve(Fixture\AbstractService::class);
    }

    public function testVariadicClassService(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->never())
            ->method('has');

        $resolver = Resolver::forContainer($container);

        // Assert
        $this->expectException(Error\UnresolvableClass::class);

        // Act
        $resolver->resolve(Fixture\VariadicClassService::class);
    }

    public function testClassWithPrimitivesThatHaveNoDefaults(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->once())
            ->method('has')
            ->with(Fixture\DependencyWithNoDefaultPrimitive::class)
            ->willReturn(false);

        $resolver = Resolver::forContainer($container);

        // Assert
        $this->expectException(Error\UnresolvablePrimitive::class);

        // Act
        $resolver->resolve(Fixture\ServiceWithNoDefaultPrimitive::class);
    }

    public function testClassThatHasUnresolvablePrimitivesButWithADefault(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->once())
            ->method('has')
            ->with(Fixture\DependencyWithNoDefaultPrimitive::class)
            ->willReturn(false);

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Fixture\ParentPrimitiveService::class);

        // Assert
        $this->assertInstanceOf(Fixture\ParentPrimitiveService::class, $resolved);
    }

    public function testCallableFunction(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->never())
            ->method('has');

        $resolver = Resolver::forContainer($container);

        // Assert
        $this->expectException(Error\UnresolvablePrimitive::class);

        // Act
        $resolver->resolve(Fixture\FunctionService::class);
    }

    public function testUnionType(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->never())
            ->method('has');

        $resolver = Resolver::forContainer($container);

        // Assert
        $this->expectException(Error\UnresolvableUnionType::class);

        // Act
        $resolver->resolve(Fixture\ServiceWithUnionType::class);
    }

    public function testPreferContainerInstancesOverNewInstances(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['has', 'get'])
            ->getMock();

        $dependency = new Fixture\SimpleDependency();

        $container->expects($this->once())
            ->method('has')
            ->with(Fixture\SimpleDependency::class)
            ->willReturn(true);

        $container->expects($this->once())
            ->method('get')
            ->with(Fixture\SimpleDependency::class)
            ->willReturn($dependency);

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Fixture\SimpleService::class);

        // Assert
        $this->assertSame($dependency, $resolved->dependency);
    }

    public function testCabinetEventDispatcher(): void
    {
        // Arrange
        $dispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->onlyMethods(['dispatch'])
            ->getMock();

        $matcher = $this->exactly(3);
        $dispatcher->expects($matcher)
            ->method('dispatch')
            ->willReturnCallback(fn(CabinetEvent $event) =>
                match ($matcher->numberOfInvocations()) {
                    0 => $this->fail('ServiceRequested event should have been dispatched.'),
                    1 => $this->assertTrue(
                        $event instanceof ServiceResolved &&
                        $event->service() === $dispatcher,
                        "First event was not ServiceResolved, or the service was not the dispatcher."
                    ),
                    2 => $this->assertTrue(
                        $event instanceof ServiceRequested &&
                        $event->serviceName() === SimpleDependency::class,
                        "Second event was not ServiceRequested, or the service name was not SimpleDependency."
                    ),
                    3 => $this->assertTrue(
                        $event instanceof ServiceResolved &&
                        $event->service() instanceof SimpleDependency,
                        "Third event was not ServiceResolved, or the service was not SimpleDependency."
                    ),
                    default => $this->fail('Too many events dispatched.'),
                });

        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['has', 'get'])
            ->getMock();

        $container->expects($this->never())
            ->method('has');

        $container->expects($this->never())
            ->method('get');

        $resolver = Resolver::forContainer($container);

        // Act
        $resolver->resolve(fn() => $dispatcher);
        $resolver->resolve(SimpleDependency::class);
    }
}

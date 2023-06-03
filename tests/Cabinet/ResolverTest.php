<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Test\Cabinet\Stub;
use Arcanum\Cabinet\Error;
use Arcanum\Cabinet\Resolver;
use Arcanum\Cabinet\Container;
use Arcanum\Cabinet\EventDispatcher;
use Arcanum\Cabinet\Event\ServiceResolved;
use Arcanum\Test\Cabinet\Stub\SimpleDependency;

#[CoversClass(Resolver::class)]
#[UsesClass(Error\UnresolvableClass::class)]
#[UsesClass(Error\UnresolvablePrimitive::class)]
#[UsesClass(Error\UnresolvableUnionType::class)]
#[UsesClass(ServiceResolved::class)]
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
            ->with(Stub\SimpleDependency::class)
            ->willReturn(false);

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(Stub\SimpleService::class, $resolved);
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
        $resolved = $resolver->resolve(Stub\ConcreteService::class);

        // Assert
        $this->assertInstanceOf(Stub\ConcreteService::class, $resolved);
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
        $resolved = $resolver->resolve(Stub\PrimitiveService::class);

        // Assert
        $this->assertInstanceOf(Stub\PrimitiveService::class, $resolved);
        $this->assertSame("", $resolved->getString());
        $this->assertSame(0, $resolved->getInt());
        $this->assertSame(0.0, $resolved->getFloat());
        $this->assertSame(false, $resolved->getBool());
        $this->assertSame([], $resolved->getArray());
        $this->assertInstanceOf(\stdClass::class, $resolved->getObject());
        $this->assertInstanceOf(Stub\SimpleDependency::class, $resolved->getMixed());
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
        $resolved = $resolver->resolve(Stub\VariadicPrimitiveService::class);

        // Assert
        $this->assertInstanceOf(Stub\VariadicPrimitiveService::class, $resolved);
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
        $resolved = $resolver->resolve(Stub\DefaultPrimitiveServiceWithNoType::class);

        // Assert
        $this->assertInstanceOf(Stub\DefaultPrimitiveServiceWithNoType::class, $resolved);
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
            ->with(Stub\SimpleDependency::class)
            ->willReturn(false);

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Stub\ParentService::class);

        // Assert
        $this->assertInstanceOf(Stub\ParentService::class, $resolved);
        $this->assertInstanceOf(Stub\SimpleDependency::class, $resolved->dependency);
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
        $resolver->resolve(Stub\AbstractService::class);
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
        $resolver->resolve(Stub\VariadicClassService::class);
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
            ->with(Stub\DependencyWithNoDefaultPrimitive::class)
            ->willReturn(false);

        $resolver = Resolver::forContainer($container);

        // Assert
        $this->expectException(Error\UnresolvablePrimitive::class);

        // Act
        $resolver->resolve(Stub\ServiceWithNoDefaultPrimitive::class);
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
            ->with(Stub\DependencyWithNoDefaultPrimitive::class)
            ->willReturn(false);

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Stub\ParentPrimitiveService::class);

        // Assert
        $this->assertInstanceOf(Stub\ParentPrimitiveService::class, $resolved);
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
        $resolver->resolve(Stub\FunctionService::class);
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
        $resolver->resolve(Stub\ServiceWithUnionType::class);
    }

    public function testPreferContainerInstancesOverNewInstances(): void
    {
        // Arrange
        /** @var Container&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['has', 'get'])
            ->getMock();

        $dependency = new Stub\SimpleDependency();

        $container->expects($this->once())
            ->method('has')
            ->with(Stub\SimpleDependency::class)
            ->willReturn(true);

        $container->expects($this->once())
            ->method('get')
            ->with(Stub\SimpleDependency::class)
            ->willReturn($dependency);

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Stub\SimpleService::class);

        // Assert
        $this->assertSame($dependency, $resolved->dependency);
    }

    public function testCabinetEventDispatchersDispatchServiceResolved(): void
    {
        // Arrange
        $dispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->onlyMethods(['dispatch'])
            ->getMock();

        $dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with($this->isInstanceOf(ServiceResolved::class));

        $this->assertInstanceOf(EventDispatcher::class, $dispatcher);

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

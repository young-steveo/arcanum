<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Test\Codex\Fixture;
use Arcanum\Codex\Error;
use Arcanum\Codex\Resolver;
use Arcanum\Codex\EventDispatcher;
use Arcanum\Codex\Event\CodexEvent;
use Arcanum\Codex\Event\ClassResolved;
use Arcanum\Codex\Event\ClassRequested;
use Arcanum\Test\Codex\Fixture\SimpleDependency;
use Arcanum\Test\Codex\Fixture\SimpleClass;
use Psr\Container\ContainerInterface;

#[CoversClass(Resolver::class)]
#[UsesClass(Error\UnresolvableClass::class)]
#[UsesClass(Error\UnresolvablePrimitive::class)]
#[UsesClass(Error\UnresolvableUnionType::class)]
#[UsesClass(ClassResolved::class)]
#[UsesClass(ClassRequested::class)]
final class ResolverTest extends TestCase
{
    public function testClosure(): void
    {
        // Arrange
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        $resolved = $resolver->resolve(Fixture\SimpleClass::class);

        // Assert
        $this->assertInstanceOf(Fixture\SimpleClass::class, $resolved);
    }

    public function testClassWithConstructorThatHasNoParameters(): void
    {
        // Arrange
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container->expects($this->never())
            ->method('get');

        $container->expects($this->never())
            ->method('has');

        $resolver = Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(Fixture\ConcreteClass::class);

        // Assert
        $this->assertInstanceOf(Fixture\ConcreteClass::class, $resolved);
    }

    public function testClassWithDefaultPrimitives(): void
    {
        // Arrange
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        $resolved = $resolver->resolve(Fixture\ParentClass::class);

        // Assert
        $this->assertInstanceOf(Fixture\ParentClass::class, $resolved);
        $this->assertInstanceOf(Fixture\SimpleDependency::class, $resolved->dependency);
    }

    public function testAnUninstantiableClass(): void
    {
        // Arrange
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        $resolver->resolve(Fixture\AbstractClass::class);
    }

    public function testVariadicClassService(): void
    {
        // Arrange
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
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
        $resolved = $resolver->resolve(Fixture\SimpleClass::class);

        // Assert
        $this->assertSame($dependency, $resolved->dependency);
    }

    public function testCodexEventDispatcher(): void
    {
        // Arrange
        $dispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->onlyMethods(['dispatch'])
            ->getMock();

        $matcher = $this->exactly(5);
        $dispatcher->expects($matcher)
            ->method('dispatch')
            ->willReturnCallback(fn(CodexEvent $event) =>
                match ($matcher->numberOfInvocations()) {
                    0 => $this->fail('ClassRequested event should have been dispatched.'),
                    1 => $this->assertTrue(
                        $event instanceof ClassResolved &&
                        $event->class() === $dispatcher,
                        "First event was not ClassResolved, or the service was not the dispatcher."
                    ),
                    2 => $this->assertTrue(
                        $event instanceof ClassRequested &&
                        $event->className() === SimpleClass::class,
                        "Second event was not ClassRequested, or the service name was not SimpleClass."
                    ),
                    3 => $this->assertTrue(
                        $event instanceof ClassRequested &&
                        $event->className() === SimpleDependency::class,
                        "Third event was not ClassRequested, or the service name was not SimpleDependency."
                    ),
                    4 => $this->assertTrue(
                        $event instanceof ClassResolved &&
                        $event->class() instanceof SimpleDependency,
                        "Fourth event was not ClassResolved, or the service was not an instance of SimpleDependency."
                    ),
                    5 => $this->assertTrue(
                        $event instanceof ClassResolved &&
                        $event->class() instanceof SimpleClass,
                        "Fifth event was not ClassResolved, or the service was not an instance of SimpleClass."
                    ),
                    default => $this->fail('Too many events dispatched.'),
                });

        /** @var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject */
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['has', 'get'])
            ->getMock();

        $container->expects($this->once())
            ->method('has')
            ->with(Fixture\SimpleDependency::class)
            ->willReturn(false);

        $container->expects($this->never())
            ->method('get');

        $resolver = Resolver::forContainer($container);

        // Act
        $resolver->resolve(fn() => $dispatcher);
        $resolver->resolve(SimpleClass::class);
    }
}

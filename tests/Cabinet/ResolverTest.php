<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(\Arcanum\Cabinet\Resolver::class)]
#[UsesClass(\Arcanum\Cabinet\Container::class)]
#[UsesClass(\Arcanum\Cabinet\Error\InvalidKey::class)]
#[UsesClass(\Arcanum\Cabinet\Error\UnresolvableClass::class)]
#[UsesClass(\Arcanum\Cabinet\Error\UnresolvablePrimitive::class)]
#[UsesClass(\Arcanum\Cabinet\Error\UnresolvableUnionType::class)]
final class ResolverTest extends TestCase
{
    public function testResolverCanResolveClosure(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(fn (): object => new \stdClass());

        // Assert
        $this->assertInstanceOf(\stdClass::class, $resolved);
    }

    public function testResolverCanResolveClassWithNoConstructor(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(\Arcanum\Test\Cabinet\Stub\SimpleService::class);

        // Assert
        $this->assertInstanceOf(\Arcanum\Test\Cabinet\Stub\SimpleService::class, $resolved);
    }

    public function testResolverCanResolveClassWithConstructorThatHasNoParameters(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(\Arcanum\Test\Cabinet\Stub\ConcreteService::class);

        // Assert
        $this->assertInstanceOf(\Arcanum\Test\Cabinet\Stub\ConcreteService::class, $resolved);
    }

    public function testResolverCanResolveClassWithDefaultPrimitives(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(\Arcanum\Test\Cabinet\Stub\PrimitiveService::class);

        // Assert
        $this->assertInstanceOf(\Arcanum\Test\Cabinet\Stub\PrimitiveService::class, $resolved);
        $this->assertSame("", $resolved->getString());
        $this->assertSame(0, $resolved->getInt());
        $this->assertSame(0.0, $resolved->getFloat());
        $this->assertSame(false, $resolved->getBool());
        $this->assertSame([], $resolved->getArray());
        $this->assertInstanceOf(\stdClass::class, $resolved->getObject());
        $this->assertInstanceOf(\Arcanum\Test\Cabinet\Stub\SimpleDependency::class, $resolved->getMixed());
        $this->assertNull($resolved->getNull());
    }

    public function testResolverCanResolveVariadicPrimitives(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(\Arcanum\Test\Cabinet\Stub\VariadicPrimitiveService::class);

        // Assert
        $this->assertInstanceOf(\Arcanum\Test\Cabinet\Stub\VariadicPrimitiveService::class, $resolved);
        $this->assertSame([], $resolved->strings);
    }

    public function testResolverCanResolveDefaultValuesWithNoTypeHint(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(\Arcanum\Test\Cabinet\Stub\DefaultPrimitiveServiceWithNoType::class);

        // Assert
        $this->assertInstanceOf(\Arcanum\Test\Cabinet\Stub\DefaultPrimitiveServiceWithNoType::class, $resolved);
        $this->assertSame(0, $resolved->test);
    }

    public function testResolverCanResolveParentKeywordInDependencies(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Act
        $resolved = $resolver->resolve(\Arcanum\Test\Cabinet\Stub\ParentService::class);

        // Assert
        $this->assertInstanceOf(\Arcanum\Test\Cabinet\Stub\ParentService::class, $resolved);
        $this->assertInstanceOf(\Arcanum\Test\Cabinet\Stub\SimpleDependency::class, $resolved->dependency);
    }

    // public function testResolverCannotResolveUnknownService(): void
    // {
    //     // Arrange
    //     $container = new \Arcanum\Cabinet\Container();
    //     $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

    //     // Act
    //     $this->expectException(\Arcanum\Cabinet\Error\UnknownClass::class);

    //     // Assert
    //     $resolver->resolve(\Arcanum\Test\Cabinet\Stub\UnknownService::class);
    // }

    public function testResolverCannotResolveAnUninstantiableClass(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Assert
        $this->expectException(\Arcanum\Cabinet\Error\UnresolvableClass::class);

        // Act
        $resolver->resolve(\Arcanum\Test\Cabinet\Stub\AbstractService::class);
    }

    public function testResolverCannotResolveVariadicClassService(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Assert
        $this->expectException(\Arcanum\Cabinet\Error\UnresolvableClass::class);

        // Act
        $resolver->resolve(\Arcanum\Test\Cabinet\Stub\VariadicClassService::class);
    }

    public function testResolverCannotResolveClassWithPrimitivesThatHaveNoDefaults(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Assert
        $this->expectException(\Arcanum\Cabinet\Error\UnresolvablePrimitive::class);

        // Act
        $resolver->resolve(\Arcanum\Test\Cabinet\Stub\ServiceWithNoDefaultPrimitive::class);
    }

    public function testResolverCannotResolveCallableFunction(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Assert
        $this->expectException(\Arcanum\Cabinet\Error\UnresolvablePrimitive::class);

        // Act
        $resolver->resolve(\Arcanum\Test\Cabinet\Stub\FunctionService::class);
    }

    public function testResolverCannotResolveUnionType(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $resolver = \Arcanum\Cabinet\Resolver::forContainer($container);

        // Assert
        $this->expectException(\Arcanum\Cabinet\Error\UnresolvableUnionType::class);

        // Act
        $resolver->resolve(\Arcanum\Test\Cabinet\Stub\ServiceWithUnionType::class);
    }
}

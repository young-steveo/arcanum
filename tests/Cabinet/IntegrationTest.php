<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Cabinet\Container;
use Arcanum\Test\Fixture;
use Arcanum\Test\Flow\Continuum\Fixture\BasicProgression;

/**
 * Tests that require the full container.
 */
#[CoversClass(Container::class)]
#[UsesClass(\Arcanum\Codex\Resolver::class)]
#[UsesClass(\Arcanum\Flow\Pipeline\Pipeline::class)]
#[UsesClass(\Arcanum\Flow\Pipeline\StandardProcessor::class)]
#[UsesClass(\Arcanum\Flow\Pipeline\PipelayerSystem::class)]
#[UsesClass(\Arcanum\Cabinet\SimpleProvider::class)]
#[UsesClass(\Arcanum\Cabinet\PrototypeProvider::class)]
#[UsesClass(\Arcanum\Flow\Continuum\Continuum::class)]
#[UsesClass(\Arcanum\Flow\Continuum\StandardAdvancer::class)]
#[UsesClass(\Arcanum\Flow\Continuum\ContinuationCollection::class)]
final class IntegrationTest extends TestCase
{
    public function testDecorators(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\ClassResolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\ClassResolver::class)
            ->onlyMethods(['resolve', 'resolveWith'])
            ->getMock();

        $original = new Fixture\SimpleService(new Fixture\SimpleDependency());
        $second = new Fixture\SimpleService(new Fixture\SimpleDependency());
        $third = new Fixture\SimpleService(new Fixture\SimpleDependency());

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(Fixture\SimpleService::class)
            ->willReturnCallback(fn () => $original);

        $container = new Container($resolver);
        $container->service(Fixture\SimpleService::class);

        $container->decorator(
            serviceName: Fixture\SimpleService::class,
            decorator: fn() => $second
        );

        $count = 0;
        $container->decorator(
            serviceName: Fixture\SimpleService::class,
            decorator: function () use ($third, &$count) {
                $count++;
                if ($count > 1) {
                    $this->fail('Decorator was called more than once.');
                }
                return $third;
            }
        );

        // Act
        $result = $container->get(Fixture\SimpleService::class);
        $shouldBeSame = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertSame($third, $result);
        $this->assertSame($third, $shouldBeSame);
    }

    public function testDecoratorsOnPrototypes(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\ClassResolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\ClassResolver::class)
            ->onlyMethods(['resolve', 'resolveWith'])
            ->getMock();

        $original = new Fixture\SimpleService(new Fixture\SimpleDependency());
        $second = new Fixture\SimpleService(new Fixture\SimpleDependency());
        $third = new Fixture\SimpleService(new Fixture\SimpleDependency());

        $resolver->expects($this->exactly(2))
            ->method('resolve')
            ->with(Fixture\SimpleService::class)
            ->willReturnCallback(fn () => $original);

        $container = new Container($resolver);
        $container->prototype(Fixture\SimpleService::class);

        $container->decorator(
            serviceName: Fixture\SimpleService::class,
            decorator: fn() => $second
        );

        $container->decorator(
            serviceName: Fixture\SimpleService::class,
            decorator: fn() => $third
        );

        // Act
        $result = $container->get(Fixture\SimpleService::class);
        $shouldBeSame = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertSame($third, $result);
        $this->assertSame($third, $shouldBeSame);
    }

    public function testMiddleware(): void
    {
        // Arrange
        /** @var \Arcanum\Codex\ClassResolver&\PHPUnit\Framework\MockObject\MockObject */
        $resolver = $this->getMockBuilder(\Arcanum\Codex\ClassResolver::class)
            ->onlyMethods(['resolve', 'resolveWith'])
            ->getMock();

        $service = new Fixture\SimpleService(new Fixture\SimpleDependency());

        $resolver->expects($this->once())
            ->method('resolve')
            ->with(Fixture\SimpleService::class)
            ->willReturnCallback(fn () => $service);

        $container = new Container($resolver);
        $container->service(Fixture\SimpleService::class);

        $container->middleware(
            serviceName: Fixture\SimpleService::class,
            middleware: BasicProgression::fromClosure(fn(object $service, callable $next) => $next())
        );

        $count = 0;
        $container->middleware(
            serviceName: Fixture\SimpleService::class,
            middleware: BasicProgression::fromClosure(function (object $service, callable $next) use (&$count) {
                $count++;
                $next();
            })
        );

        // Act
        $result = $container->get(Fixture\SimpleService::class);
        $shouldBeSame = $container->get(Fixture\SimpleService::class);

        // Assert
        $this->assertSame($service, $result);
        $this->assertSame($service, $shouldBeSame);
        $this->assertSame(2, $count);
    }
}

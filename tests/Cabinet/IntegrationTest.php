<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Cabinet\Container;
use Arcanum\Test\Fixture;

/**
 * Tests that require the full container.
 */
#[CoversClass(Container::class)]
#[UsesClass(\Arcanum\Codex\Resolver::class)]
#[UsesClass(\Arcanum\Flow\Pipeline::class)]
#[UsesClass(\Arcanum\Flow\StandardProcessor::class)]
#[UsesClass(\Arcanum\Cabinet\SimpleProvider::class)]
#[UsesClass(\Arcanum\Cabinet\PrototypeProvider::class)]
final class IntegrationTest extends TestCase
{
    public function testCreateContainer(): void
    {
        // Assert
        $this->assertInstanceOf(Container::class, Container::create());
    }

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

        $container = Container::fromResolver($resolver);
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

        $container = Container::fromResolver($resolver);
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
}

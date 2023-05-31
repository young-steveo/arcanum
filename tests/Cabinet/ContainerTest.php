<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(\Arcanum\Cabinet\Container::class)]
#[UsesClass(\Arcanum\Cabinet\OutOfBounds::class)]
#[UsesClass(\Arcanum\Cabinet\InvalidKey::class)]
#[UsesClass(\Arcanum\Cabinet\SimpleProvider::class)]
final class ContainerTest extends TestCase
{
    public function testContainerImplementsArrayAccess(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();

        // Act
        $container['foo'] = 'bar';

        // Assert
        $this->assertEquals('bar', $container['foo']);
    }

    public function testContainerThrowsOutOfBoundsIfArrayAccessOffsetDoesNotExist(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();

        // Assert
        $this->expectException(\Arcanum\Cabinet\OutOfBounds::class);

        // Act
        $container['foo']; /** @phpstan-ignore-line */
    }

    public function testContainerOffsetUnset(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $container['foo'] = 'bar';

        // Act
        unset($container['foo']);

        // Assert
        $this->assertFalse(isset($container['foo']));
    }

    public function testContainerOnlyAcceptsStringKeys(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();

        // Assert
        $this->expectException(\Arcanum\Cabinet\InvalidKey::class);

        // Act
        $container[0] = 'bar'; /** @phpstan-ignore-line */
    }

    public function testContainerCannotAccessNonStringKey(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();

        // Assert
        $this->expectException(\Arcanum\Cabinet\InvalidKey::class);

        // Act
        $container[0]; /** @phpstan-ignore-line */
    }

    public function testContainerGetService(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $container['foo'] = 'bar';

        // Act
        $result = $container->get('foo');

        // Assert
        $this->assertEquals('bar', $result);
    }

    public function testContainerGetThrowsOutOfBoundsIfServiceDoesNotExist(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();

        // Assert
        $this->expectException(\Arcanum\Cabinet\OutOfBounds::class);

        // Act
        $container->get('foo');
    }

    public function testContainerHas(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $container['foo'] = 'bar';

        // Act
        $result = $container->has('foo');

        // Assert
        $this->assertTrue($result);
    }

    public function testContainerProvider(): void
    {
        // Arrange
        /** @var \Arcanum\Cabinet\Provider&\PHPUnit\Framework\MockObject\MockObject */
        $provider = $this->getMockBuilder(\Arcanum\Cabinet\Provider::class)
            ->onlyMethods(['__invoke'])
            ->getMock();

        $provider->expects($this->once())
            ->method('__invoke')
            ->willReturn('bar');

        $container = new \Arcanum\Cabinet\Container();

        // Act
        $container->provider('foo', $provider);
        $result = $container->get('foo');

        // Assert
        $this->assertEquals('bar', $result);
    }

    public function testContainerFactory(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $container->factory('foo', fn() => 'bar');

        // Act
        $result = $container->get('foo');

        // Assert
        $this->assertEquals('bar', $result);
    }

    public function testContainerService(): void
    {
        // Arrange
        $container = new \Arcanum\Cabinet\Container();
        $container->service('foo', 'bar');

        // Act
        $result = $container->get('foo');

        // Assert
        $this->assertEquals('bar', $result);
    }
}

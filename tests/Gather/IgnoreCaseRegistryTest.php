<?php

declare(strict_types=1);

namespace Arcanum\Test\Gather;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Gather\IgnoreCaseRegistry;
use Arcanum\Gather\Registry;

#[CoversClass(IgnoreCaseRegistry::class)]
#[UsesClass(Registry::class)]
final class IgnoreCaseRegistryTest extends TestCase
{
    public function testHasAndGet(): void
    {
        // Arrange
        $registry = new IgnoreCaseRegistry(['foo' => 'bar']);

        // Act
        $has = $registry->has('fOo');
        $get = $registry->get('foO');
        $nope = $registry->has('nope');

        // Assert
        $this->assertTrue($has);
        $this->assertSame('bar', $get);
        $this->assertFalse($nope);
    }

    public function testGetSetViaArrayAccess(): void
    {
        // Arrange
        $registry = new IgnoreCaseRegistry(['foo' => 'bar']);

        // Act
        $set = isset($registry['fOo']);
        $registry['Foo'] = 'baz';
        $get = $registry['foO'];
        unset($registry['FOO']);
        $empty = $registry['FoO'];

        // Assert
        $this->assertSame('baz', $get);
        $this->assertTrue($set);
        $this->assertNull($empty);
    }

    public function testOffsetSetGetExistsUnset(): void
    {
        // Arrange
        $registry = new IgnoreCaseRegistry(['foo' => 'bar']);

        // Act
        $set = $registry->offsetExists('fOo');
        $registry->offsetSet('Foo', 'baz');
        $get = $registry->offsetGet('foO');
        $registry->offsetUnset('FOO');
        $empty = $registry->offsetGet('FoO');

        // Assert
        $this->assertSame('baz', $get);
        $this->assertTrue($set);
        $this->assertNull($empty);
    }

    public function testGetString(): void
    {
        // Arrange
        $registry = new IgnoreCaseRegistry(['foo' => '1b1a@1r1']);

        // Act
        $get = $registry->asString('FOo');

        // Assert
        $this->assertSame('1b1a@1r1', $get);
    }

    public function testGetInt(): void
    {
        // Arrange
        $registry = new IgnoreCaseRegistry(['foo' => '1b1a@1r1']);

        // Act
        $get = $registry->asInt('FoO');

        // Assert
        $this->assertSame(1, $get);
    }

    public function testGetFloat(): void
    {
        // Arrange
        $registry = new IgnoreCaseRegistry(['foo' => '1b1a@1r1']);

        // Act
        $get = $registry->asFloat('fOO');

        // Assert
        $this->assertSame(1.0, $get);
    }

    public function testGetBool(): void
    {
        // Arrange
        $registry = new IgnoreCaseRegistry(['foo' => '1b1a@1r1']);

        // Act
        $get = $registry->asBool('foO');

        // Assert
        $this->assertTrue($get);
    }
}

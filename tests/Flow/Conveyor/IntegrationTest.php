<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Conveyor;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\Conveyor\MiddlewareBus;
use Arcanum\Flow\Conveyor\Middleware\FinalFilter;
use Arcanum\Flow\Conveyor\Middleware\NonPublicMethodFilter;
use Arcanum\Flow\Conveyor\Middleware\NonStaticPropertyFilter;
use Arcanum\Flow\Conveyor\Middleware\PublicPropertyFilter;
use Arcanum\Flow\Conveyor\Middleware\ReadOnlyPropertyFilter;
use Arcanum\Cabinet\Container;
use Arcanum\Test\Flow\Conveyor\Fixture\DoSomething;
use Arcanum\Test\Flow\Conveyor\Fixture\DoSomethingResult;

#[CoversClass(MiddlewareBus::class)]
#[UsesClass(\Arcanum\Flow\Pipeline\Pipeline::class)]
#[UsesClass(\Arcanum\Flow\Pipeline\StandardProcessor::class)]
#[UsesClass(\Arcanum\Flow\Pipeline\PipelayerSystem::class)]
#[UsesClass(\Arcanum\Flow\Continuum\Continuum::class)]
#[UsesClass(\Arcanum\Flow\Continuum\StandardAdvancer::class)]
#[UsesClass(\Arcanum\Flow\Continuum\ContinuationCollection::class)]
#[UsesClass(Container::class)]
#[UsesClass(\Arcanum\Cabinet\PrototypeProvider::class)]
#[UsesClass(\Arcanum\Codex\Event\ClassRequested::class)]
#[UsesClass(\Arcanum\Codex\Resolver::class)]
#[UsesClass(FinalFilter::class)]
#[UsesClass(NonPublicMethodFilter::class)]
#[UsesClass(NonStaticPropertyFilter::class)]
#[UsesClass(PublicPropertyFilter::class)]
#[UsesClass(ReadOnlyPropertyFilter::class)]
final class IntegrationTest extends TestCase
{
    public function testSimpleDispatch(): void
    {
        // Arrange
        $bus = new MiddlewareBus(new Container());

        // Act
        $result = $bus->dispatch(new DoSomething('test'));

        // Assert
        $this->assertInstanceOf(DoSomethingResult::class, $result);
        $this->assertSame('test', $result->name);
    }

    public function testDispatchWithAllFilters(): void
    {
        // Arrange
        $bus = new MiddlewareBus(new Container());
        $bus->before(
            new FinalFilter(),
            new NonPublicMethodFilter(),
            new NonStaticPropertyFilter(),
            new PublicPropertyFilter(),
            new ReadOnlyPropertyFilter(),
        );

        $bus->after(
            new FinalFilter(),
            new NonPublicMethodFilter(),
            new NonStaticPropertyFilter(),
            new PublicPropertyFilter(),
            new ReadOnlyPropertyFilter(),
        );

        // Act
        $result = $bus->dispatch(new DoSomething('test'));

        // Assert
        $this->assertInstanceOf(DoSomethingResult::class, $result);
        $this->assertSame('test', $result->name);
    }
}

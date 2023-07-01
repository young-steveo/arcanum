<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\River;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreMethodForCodeCoverage;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\River\LazyResource;
use Arcanum\Flow\River\StreamResource;
use Arcanum\Flow\River\InvalidSource;

#[CoversClass(LazyResource::class)]
#[UsesClass(InvalidSource::class)]
#[UsesClass(StreamResource::class)]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'isResource')]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'feof')]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'ftell')]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'fseek')]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'fclose')]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'streamGetMetaData')]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'clearstatcache')]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'fstat')]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'fread')]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'fwrite')]
#[IgnoreMethodForCodeCoverage(LazyResource::class, 'streamGetContents')]
final class LazyResourceTest extends TestCase
{
    public function testLazyResource(): void
    {
        // Arrange
        $streamResource = LazyResource::for('php://memory', 'r');

        // Act
        $isResource = $streamResource->isResource();

        // Assert
        $this->assertTrue($isResource);

        // Cleanup
        fclose($streamResource->export());
    }

    public function testExportReturnsTheResource(): void
    {
        // Arrange
        $streamResource = LazyResource::for('php://memory', 'r');

        // Act
        $exportedResource = $streamResource->export();

        // Assert
        $this->assertIsResource($exportedResource);

        // Cleanup
        fclose($exportedResource);
    }

    /**
     * @return resource|false
     */
    public function getResource()
    {
        return fopen('php://memory', 'r');
    }

    public function testFromWithArrayCallable(): void
    {
        // Arrange
        $streamResource = LazyResource::from([$this, 'getResource']);

        // Act
        $isResource = $streamResource->isResource();

        // Assert
        $this->assertTrue($isResource);

        // Cleanup
        fclose($streamResource->export());
    }

    public function testWillThrowInvalidSourceIfCallableDoesNotReturnResource(): void
    {
        // Arrange
        $streamResource = LazyResource::from(fn() => false);

        // Act
        $this->expectException(InvalidSource::class);
        $this->expectExceptionMessage('Stream source must be a live resource');
        $streamResource->isResource();
    }
}

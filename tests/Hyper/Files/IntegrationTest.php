<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\Files;

use Arcanum\Hyper\Files\UploadedFile;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Arcanum\Flow\River\LazyResource;
use Arcanum\Flow\River\Stream;
use Arcanum\Flow\River\StreamResource;
use Arcanum\Flow\River\Bank;
use Arcanum\Hyper\Files\Error;
use Arcanum\Hyper\Files\InvalidFile;
use Arcanum\Gather\Registry;

#[CoversClass(UploadedFile::class)]
#[UsesClass(Error::class)]
#[UsesClass(Stream::class)]
#[UsesClass(StreamResource::class)]
#[UsesClass(LazyResource::class)]
#[UsesClass(InvalidFile::class)]
#[UsesClass(Bank::class)]
#[UsesClass(Registry::class)]
final class IntegrationTest extends TestCase
{
    public function testMoveTo(): void
    {
        // Arrange
        $temporaryFile = \tempnam(\sys_get_temp_dir(), 'phpunit');

        if ($temporaryFile === false) {
            $this->fail('Failed to create temporary file');
        }

        $uploadedFile = new UploadedFile(
            file: $temporaryFile,
            mode: 'w+',
        );

        $targetFilePath = \sys_get_temp_dir() . '/phpunit-target';

        // Act
        $uploadedFile->moveTo($targetFilePath);

        // Assert
        $this->assertFileDoesNotExist($temporaryFile);
        $this->assertFileExists($targetFilePath);

        // Cleanup
        \unlink($targetFilePath);
    }

    public function testMoveToWithStream(): void
    {
        // Arrange
        $temporaryFile = \tempnam(\sys_get_temp_dir(), 'phpunit');

        if ($temporaryFile === false) {
            $this->fail('Failed to create temporary file');
        }

        $pointer = \fopen($temporaryFile, 'w+');

        if ($pointer === false) {
            $this->fail('Failed to open temporary file');
        }

        \fwrite($pointer, 'test');

        \fseek($pointer, 0);

        $stream = new Stream(
            source: StreamResource::wrap($pointer),
        );

        $uploadedFile = new UploadedFile(
            file: $stream,
            mode: 'w+',
        );

        $targetFilePath = \sys_get_temp_dir() . '/phpunit-target';

        // Act
        $uploadedFile->moveTo($targetFilePath);

        // Assert
        $this->assertFileDoesNotExist($temporaryFile);
        $this->assertFileExists($targetFilePath);

        // Cleanup
        \unlink($targetFilePath);
    }

    public function testMoveToThrowsInvalidFileIfItHasAlreadyBeenMoved(): void
    {
        // Arrange
        $temporaryFile = \tempnam(\sys_get_temp_dir(), 'phpunit');

        if ($temporaryFile === false) {
            $this->fail('Failed to create temporary file');
        }

        $uploadedFile = new UploadedFile(
            file: $temporaryFile,
            mode: 'w+',
        );

        $targetFilePath = \sys_get_temp_dir() . '/phpunit-target';

        // Act
        $uploadedFile->moveTo($targetFilePath);

        // Assert
        $this->assertFileDoesNotExist($temporaryFile);
        $this->assertFileExists($targetFilePath);

        // Cleanup
        \unlink($targetFilePath);

        // Assert
        $this->expectException(\Arcanum\Hyper\Files\InvalidFile::class);

        // Act
        $uploadedFile->moveTo($targetFilePath);
    }
}

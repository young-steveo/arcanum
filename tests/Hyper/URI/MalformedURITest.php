<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\URI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Arcanum\Hyper\URI\MalformedURI::class)]
final class MalformedURITest extends TestCase
{
    public function testInvalidSource(): void
    {
        // Arrange
        $error = new \Arcanum\Hyper\URI\MalformedURI('foo');

        // Act
        $message = $error->getMessage();

        // Assert
        $this->assertEquals('Malformed URI: foo', $message);
    }
}

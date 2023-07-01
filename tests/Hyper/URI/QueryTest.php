<?php

declare(strict_types=1);

namespace Arcanum\Test\Hyper\URI;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Arcanum\Hyper\URI\Query;

#[CoversClass(Query::class)]
final class QueryTest extends TestCase
{
    public function testQuery(): void
    {
        // Arrange
        $query = new Query('query=foo');

        // Act
        $data = (string)$query;

        // Assert
        $this->assertSame('query=foo', $data);
    }

    public function testQueryEncodesNonURLCharacters(): void
    {
        // Arrange
        $query = new Query('?query=foo&bar=b az');

        // Act
        $data = (string)$query;

        // Assert
        $this->assertSame('?query=foo&bar=b%20az', $data);
    }
}

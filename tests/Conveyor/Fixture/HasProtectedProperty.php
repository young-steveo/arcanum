<?php

declare(strict_types=1);

namespace Arcanum\Test\Conveyor\Fixture;

final class HasProtectedProperty
{
    public function __construct(protected readonly string $name = "Not Allowed")
    {
    }
}

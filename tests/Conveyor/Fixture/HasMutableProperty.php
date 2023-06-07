<?php

declare(strict_types=1);

namespace Arcanum\Test\Conveyor\Fixture;

final class HasMutableProperty
{
    public function __construct(public string $name)
    {
    }
}

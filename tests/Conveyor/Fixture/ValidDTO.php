<?php

declare(strict_types=1);

namespace Arcanum\Test\Conveyor\Fixture;

final class ValidDTO
{
    private function __construct(
        public readonly string $name
    ) {
    }

    public static function fromName(string $name): static
    {
        return new static($name);
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Fixture;

class DependencyWithNoDefaultPrimitive
{
    /**
     * @param int[] $array
     */
    public function __construct(
        private string $string,
        private int $int,
        private float $float,
        private bool $bool,
        private array $array,
        private object $object,
        private mixed $mixed,
        private null $null,
    ) {
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function getInt(): int
    {
        return $this->int;
    }

    public function getFloat(): float
    {
        return $this->float;
    }

    public function getBool(): bool
    {
        return $this->bool;
    }

    /**
     * @return int[]
     */
    public function getArray(): array
    {
        return $this->array;
    }

    public function getObject(): object
    {
        return $this->object;
    }

    public function getMixed(): mixed
    {
        return $this->mixed;
    }

    public function getNull(): null
    {
        return $this->null;
    }
}

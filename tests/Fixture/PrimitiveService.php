<?php

declare(strict_types=1);

namespace Arcanum\Test\Fixture;

class PrimitiveService
{
    /**
     * @param int[] $array
     */
    public function __construct(
        private string $string = "",
        private int $int = 0,
        private float $float = 0.0,
        private bool $bool = false,
        private array $array = [],
        private object $object = new \stdClass(),
        private mixed $mixed = new SimpleDependency(),
        private null $null = null,
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

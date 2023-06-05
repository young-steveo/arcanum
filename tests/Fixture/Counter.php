<?php

declare(strict_types=1);

namespace Arcanum\Test\Fixture;

final class Counter
{
    private int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }

    public function count(): int
    {
        return $this->count;
    }
}

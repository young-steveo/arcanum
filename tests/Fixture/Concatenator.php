<?php

declare(strict_types=1);

namespace Arcanum\Test\Fixture;

final class Concatenator
{
    private string $data = '';

    public function add(string $text): void
    {
        $this->data .= $text;
    }

    public function __toString(): string
    {
        return $this->data;
    }
}

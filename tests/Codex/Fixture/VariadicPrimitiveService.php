<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Fixture;

class VariadicPrimitiveService
{
    /**
     * @var string[]
     */
    public array $strings;

    /**
     * @param string ...$strings
     */
    public function __construct(string ...$strings)
    {
        $this->strings = $strings;
    }
}

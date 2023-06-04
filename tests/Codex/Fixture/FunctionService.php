<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Fixture;

class FunctionService
{
    public string $name;
    public function __construct(callable $dependency)
    {
        $this->name = $dependency();
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Fixture\Command;

final class DoSomethingResult
{
    public function __construct(public readonly string $name)
    {
    }
}

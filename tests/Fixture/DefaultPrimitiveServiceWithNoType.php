<?php

declare(strict_types=1);

namespace Arcanum\Test\Fixture;

class DefaultPrimitiveServiceWithNoType
{
    public function __construct(public $test = 0)  /** @phpstan-ignore-line */
    {
    }
}

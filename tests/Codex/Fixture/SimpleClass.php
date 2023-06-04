<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Fixture;

class SimpleClass
{
    public function __construct(
        public SimpleDependency $dependency
    ) {
    }
}

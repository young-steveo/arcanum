<?php

declare(strict_types=1);

namespace Arcanum\Test\Fixture;

class SimpleService
{
    public function __construct(
        public SimpleDependency $dependency
    ) {
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Fixture;

class SimpleService
{
    public function __construct(
        public SimpleDependency $dependency
    ) {
    }
}

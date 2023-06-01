<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Stub;

class SimpleService
{
    public function __construct(
        public SimpleDependency $dependency
    ) {
    }
}

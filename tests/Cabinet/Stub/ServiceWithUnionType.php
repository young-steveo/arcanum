<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Stub;

class ServiceWithUnionType
{
    public function __construct(
        public SimpleDependency|bool $dependency
    ) {
    }
}

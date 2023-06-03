<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Fixture;

class ServiceWithUnionType
{
    public function __construct(
        public SimpleDependency|bool $dependency
    ) {
    }
}

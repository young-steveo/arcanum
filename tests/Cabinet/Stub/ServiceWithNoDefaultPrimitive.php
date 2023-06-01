<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Stub;

class ServiceWithNoDefaultPrimitive
{
    public function __construct(
        private DependencyWithNoDefaultPrimitive $dependency,
    ) {
    }

    public function getDependency(): DependencyWithNoDefaultPrimitive
    {
        return $this->dependency;
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Fixture;

class ParentPrimitiveService
{
    public DependencyWithNoDefaultPrimitive $dependency;
    public function __construct(DependencyWithNoDefaultPrimitive $dependency = new DependencyWithNoDefaultPrimitive(
        '',
        0,
        0.0,
        false,
        [],
        new \stdClass(),
        null,
        null,
    ))
    {
        $this->dependency = $dependency;
    }
}

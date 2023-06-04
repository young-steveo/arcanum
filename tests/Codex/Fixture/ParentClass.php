<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Fixture;

class ParentClass extends SimpleClass
{
    public SimpleDependency $dependency;
    public function __construct(SimpleDependency $dependency)
    {
        $this->dependency = $dependency;
    }
}

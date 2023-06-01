<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Stub;

class ParentService extends SimpleDependency
{
    public parent $dependency;
    public function __construct(parent $dependency)
    {
        $this->dependency = $dependency;
    }
}

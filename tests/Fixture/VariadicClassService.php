<?php

declare(strict_types=1);

namespace Arcanum\Test\Fixture;

class VariadicClassService
{
    /**
     * @var SimpleDependency[]
     */
    public array $dependencies;

    public function __construct(SimpleDependency ...$dependencies)
    {
        $this->dependencies = $dependencies;
    }
}

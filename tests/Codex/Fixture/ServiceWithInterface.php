<?php

declare(strict_types=1);

namespace Arcanum\Test\Codex\Fixture;

class ServiceWithInterface
{
    public function __construct(
        public ServiceInterface $dependency
    ) {
    }
}

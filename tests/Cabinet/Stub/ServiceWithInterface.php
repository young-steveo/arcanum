<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Stub;

class ServiceWithInterface
{
    public function __construct(
        public ServiceInterface $dependency
    ) {
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Fixture;

class AnotherServiceWithInterface
{
    public function __construct(
        public ServiceInterface $dependency
    ) {
    }
}

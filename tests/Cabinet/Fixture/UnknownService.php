<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Fixture;

use Arcanum\Test\Cabinet\Fixture\DoesNotExist;

class UnknownService
{
    public function __construct(public DoesNotExist $dependency)  /** @phpstan-ignore-line */
    {
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Cabinet\Stub;

use Arcanum\Test\Cabinet\Stub\DoesNotExist;

class UnknownService
{
    public function __construct(public DoesNotExist $dependency)  /** @phpstan-ignore-line */
    {
    }
}

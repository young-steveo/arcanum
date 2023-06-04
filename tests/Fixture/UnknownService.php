<?php

declare(strict_types=1);

namespace Arcanum\Test\Fixture;

use Arcanum\Test\Codex\Fixture\DoesNotExist;

class UnknownService
{
    public function __construct(public DoesNotExist $dependency)  /** @phpstan-ignore-line */
    {
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Test\Flow\Conveyor\Fixture;

final class HasPublicMethod
{
    public function doNotCallMe(): void
    {
        throw new \LogicException();
    }
}

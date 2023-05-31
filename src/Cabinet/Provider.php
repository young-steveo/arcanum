<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

abstract class Provider
{
    abstract public function __invoke(Container $container): mixed;
}

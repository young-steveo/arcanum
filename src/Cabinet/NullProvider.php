<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

final class NullProvider extends Provider
{
    /**
     * Provide null
     */
    public function __invoke(Container $container): mixed
    {
        return null;
    }
}

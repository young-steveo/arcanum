<?php

declare(strict_types=1);

namespace Arcanum\Ignition;

use Arcanum\Cabinet\Application;

interface Bootstrapper
{
    /**
     * Bootstrap the application container
     */
    public function bootstrap(Application $container): void;
}

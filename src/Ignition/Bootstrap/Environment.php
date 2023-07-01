<?php

declare(strict_types=1);

namespace Arcanum\Ignition\Bootstrap;

use Arcanum\Ignition\Bootstrapper;
use Arcanum\Cabinet\Application;
use Dotenv\Dotenv;

/**
 * The environment bootstrapper.
 */
class Environment implements Bootstrapper
{
    /**
     * Bootstrap the application.
     */
    public function bootstrap(Application $container): void
    {
        // Get the root directory from the kernel.
        /** @var \Arcanum\Ignition\Kernel $kernel */
        $kernel = $container->get(\Arcanum\Ignition\Kernel::class);
        $rootDirectry = $kernel->rootDirectory();

        // Load the environment variables from the .env file, if it exists.
        Dotenv::createImmutable($rootDirectry)->safeLoad();

        // Register the environment in the container.
        $container->factory(
            \Arcanum\Gather\Environment::class,
            fn() => new \Arcanum\Gather\Environment($_ENV)
        );
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Ignition\Bootstrap;

use Arcanum\Cabinet\Application;
use Arcanum\Ignition\Bootstrapper;
use Arcanum\Parchment\Searcher;
use Arcanum\Gather\Registry;

/**
 * The configuration bootstrapper.
 */
class Configuration implements Bootstrapper
{
    /**
     * Bootstrap the application.
     */
    public function bootstrap(Application $container): void
    {
        // Create a new configuration registry.
        $config = new Registry();

        // Register the configuration registry in the container.
        $container->instance('config', $config);

        // Get the configuration directory from the kernel.
        // @todo cache this
        /** @var \Arcanum\Ignition\Kernel $kernel */
        $kernel = $container->get(\Arcanum\Ignition\Kernel::class);
        $configDirectory = $kernel->configDirectory();

        // Load the configuration files from the config directory.
        $files = Searcher::findAll('*.php', $configDirectory);

        foreach ($files as $file) {
            $config->set(
                $file->getFilenameWithoutExtension(),
                new Registry(require $file->getRealPath()),
            );
        }
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Ignition;

use Arcanum\Hyper\RequestMethod;
use Arcanum\Flow\River\LazyResource;
use Arcanum\Flow\River\Stream;
use Arcanum\Cabinet\Application;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A HyperKernel is the initial entry point for an HTTP application.
 */
class HyperKernel implements Kernel, RequestHandlerInterface
{
    /**
     * Whether the application has been bootstrapped yet.
     */
    private bool $isBootstrapped = false;

    /**
     * The bootstrappers to run before handling a request.
     *
     * @var class-string<Bootstrapper>[]
     */
    protected array $bootstrappers = [
        Bootstrap\Environment::class,
        Bootstrap\Configuration::class,
    ];

    public function __construct(
        private string $rootDirectory,
        private string $configDirectory = '',
    ) {
        if ($configDirectory === '') {
            $this->configDirectory = $rootDirectory . DIRECTORY_SEPARATOR . 'config';
        }
    }

    /**
     * Get the root directory of the application.
     */
    public function rootDirectory(): string
    {
        return $this->rootDirectory;
    }

    /**
     * Get the configuration directory of the application.
     */
    public function configDirectory(): string
    {
        return $this->configDirectory;
    }

    /**
     * Bootstrap the application.
     */
    public function bootstrap(Application $container): void
    {
        if ($this->isBootstrapped) {
            return;
        }

        foreach ($this->bootstrappers as $name) {
            /** @var Bootstrapper $bootstrapper */
            $bootstrapper = $container->get($name);
            $bootstrapper->bootstrap($container);
        }
    }

    /**
     * Handle an incoming HTTP request.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // TEMPORARY
        return new \Arcanum\Hyper\Response(
            new \Arcanum\Hyper\Message(
                new \Arcanum\Hyper\Headers([
                    'Content-Type' => 'text/plain'
                ]),
                new Stream(LazyResource::for('php://memory', 'w+')),
                \Arcanum\Hyper\Version::v11,
            ),
            \Arcanum\Hyper\StatusCode::OK,
            \Arcanum\Hyper\Phrase::OK,
        );
    }

    /**
     * Terminate the application.
     */
    public function terminate(): void
    {
    }
}

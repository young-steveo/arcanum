<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

use Psr\Container\ContainerInterface;
use Arcanum\Codex\ClassResolver;
use Arcanum\Codex\Resolver;
use Arcanum\Flow\Pipeline\System;
use Arcanum\Flow\Pipeline\PipelayerSystem;
use Arcanum\Flow\Continuum\Collection;
use Arcanum\Flow\Continuum\ContinuationCollection;
use Arcanum\Flow\Continuum\Progression;

/**
 * @implements \ArrayAccess<class-string, mixed>
 */
class Container implements \ArrayAccess, ContainerInterface
{
    /**
     * ClassResolver used to build classes.
     */
    protected ClassResolver $resolver;

    /**
     * @var array<class-string, Provider>
     */
    protected array $providers = [];

    /**
     * @var \Arcanum\Flow\Pipeline\System
     */
    protected System $decorators;

    /**
     * @var \Arcanum\Flow\Continuum\Collection
     */
    protected Collection $middleware;

    /**
     * Container uses a resolver to instantiate services.
     */
    public function __construct(
        ClassResolver $resolver = null,
        Collection $middleware = null,
        System $decorators = null
    ) {
        $this->resolver = $resolver ?? Resolver::forContainer($this);
        $this->middleware = $middleware ?? new ContinuationCollection();
        $this->decorators = $decorators ?? new PipelayerSystem();
    }

    /**
     * Register a service on the container.
     *
     * @param class-string $serviceName
     * @param class-string|null $implementation
     */
    public function service(string $serviceName, string|null $implementation = null): void
    {
        if ($implementation === null) {
            $implementation = $serviceName;
        }
        $this->factory($serviceName, $this->simpleFactory($implementation));
    }

    /**
     * Register a service on the container while defining its dependencies.
     *
     * @param class-string $serviceName
     * @param class-string[] $dependencies
     */
    public function serviceWith(string $serviceName, array $dependencies): void
    {
        $this->factory($serviceName, function (Container $container) use ($serviceName, $dependencies) {
            return $container->resolver->resolveWith($serviceName, $dependencies);
        });
    }

    /**
     * Register a service factory on the container.
     *
     * @param class-string $serviceName
     */
    public function factory(string $serviceName, \Closure $factory): void
    {
        $this->provider($serviceName, SimpleProvider::fromFactory($factory));
    }

    /**
     * Register a service provider on the container.
     *
     * @param class-string $serviceName
     */
    public function provider(string $serviceName, Provider $provider): void
    {
        $this->providers[$serviceName] = $provider;
    }

    /**
     * Register a service instance on the container.
     *
     * @param class-string $serviceName
     */
    public function instance(string $serviceName, mixed $instance): void
    {
        $this->factory($serviceName, fn () => $instance);
    }

    /**
     * Register a prototype on the container.
     *
     * The container will create a new instance
     * of the service each time it is requested.
     *
     * @param class-string $serviceName
     */
    public function prototype(string $serviceName): void
    {
        $this->prototypeFactory($serviceName, $this->simpleFactory($serviceName));
    }

    /**
     * Register a prototype factory on the container.
     *
     * The container will create a new instance
     * of the service each time it is requested.
     *
     * @param class-string $serviceName
     */
    public function prototypeFactory(string $serviceName, \Closure $factory): void
    {
        $this->provider($serviceName, PrototypeProvider::fromFactory($factory));
    }

    /**
     * Register a decorator on the container.
     *
     * Decorators are applied to the service when it is requested from the
     * container for the first time.
     *
     * @param class-string $serviceName
     * @param callable(object): object $decorator
     */
    public function decorator(string $serviceName, callable $decorator): void
    {
        $this->decorators->pipe($serviceName, $decorator);
    }

    /**
     * Register a middleware on the container.
     *
     * Middleware are applied to the service every time it is requested from
     * the container.
     *
     * @param class-string $serviceName
     * @param Progression $middleware
     */
    public function middleware(string $serviceName, Progression $middleware): void
    {
        $this->middleware->add($serviceName, $middleware);
    }

    /**
     * @param class-string $serviceName
     */
    protected function simpleFactory(string $serviceName): \Closure
    {
        return fn(Container $container) => $container->resolver->resolve($serviceName);
    }

    /**
     * ArrayAccess methods
     */

    /**
     * offsetSet
     *
     * @param class-string $offset
     */
    public function offsetSet($offset, $value): void
    {
        if (!is_string($offset)) {
            throw new InvalidKey("Invalid key type: " . gettype($offset));
        }
        $this->providers[$offset] = SimpleProvider::fromFactory(fn() => $value);
    }

    /**
     * offsetExists
     *
     * @param class-string $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->providers[$offset]);
    }

    /**
     * offsetUnset
     *
     * @param class-string $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->providers[$offset]);
    }

    /**
     * offsetGet
     *
     * This is where all the magic happens.
     *
     * @param class-string $offset
     * @throws InvalidKey
     */
    public function offsetGet($offset): mixed
    {
        if (!is_string($offset)) {
            throw new InvalidKey("Invalid key type: " . gettype($offset));
        }

        // Provide the instance.
        $provider = $this->providers[$offset] ?? PrototypeProvider::fromFactory($this->simpleFactory($offset));

        $instance = $provider($this);

        // Apply decorators.
        $instance = $this->applyDecorators($offset, $instance, $provider);

        // Apply middleware.
        return $this->middleware->send($offset, $instance);
    }

    /**
     * Apply decorators to the instance.
     *
     * @param class-string $serviceName
     */
    protected function applyDecorators(string $serviceName, object $instance, Provider $provider): object
    {
        $instance = $this->decorators->send($serviceName, $instance);
        if (!$provider instanceof PrototypeProvider) {
            // Remove the decorators if the provider is not a prototype.
            $this->decorators->purge($serviceName);
            // Cache the instance if the provider is not a prototype.
            $this->providers[$serviceName] = SimpleProvider::fromFactory(fn() => $instance);
        }
        return $instance;
    }

    /**
     * ContainerInterface methods
     */

    /**
     * Get a service from the container.
     *
     * @param class-string $id
     */
    public function get(string $id): mixed
    {
        return $this->offsetGet($id);
    }

    /**
     * Check if a service exists in the container.
     *
     * @param class-string $id
     */
    public function has(string $id): bool
    {
        return $this->offsetExists($id);
    }
}

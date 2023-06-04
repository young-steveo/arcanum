<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

use Psr\Container\ContainerInterface;
use Arcanum\Codex\Resolver;

/**
 * @implements \ArrayAccess<class-string, mixed>
 */
class Container implements \ArrayAccess, ContainerInterface
{
    /**
     * Resolver used to build classes.
     */
    protected Resolver $resolver;

    /**
     * @var array<class-string, Provider>
     */
    protected array $providers = [];

    /**
     * Container uses a resolver to instantiate services.
     */
    protected function __construct(Resolver $resolver = null)
    {
        $this->resolver = $resolver ?? Resolver::forContainer($this);
    }

    /**
     * Create a new container.
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Create a new container from a resolver.
     */
    public static function fromResolver(Resolver $resolver): self
    {
        return new self($resolver);
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
        $this->factory($serviceName, function (Container $container) use ($implementation) {
            return $container->resolver->resolve($implementation);
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
        $this->prototypeFactory($serviceName, function (Container $container) use ($serviceName) {
            return $container->resolver->resolve($serviceName);
        });
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
            throw new Error\InvalidKey("Invalid key type: " . gettype($offset));
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
     * @param class-string $offset
     */
    public function offsetGet($offset): mixed
    {
        if (!is_string($offset)) {
            throw new Error\InvalidKey("Invalid key type: " . gettype($offset));
        }

        $provider = $this->providers[$offset] ?? new NullProvider();
        if ($instance = $provider($this)) {
            return $instance;
        }
        throw new Error\OutOfBounds("No entry was found for this identifier: $offset");
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

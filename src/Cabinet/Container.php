<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

use Psr\Container\ContainerInterface;

/**
 * @implements \ArrayAccess<class-string, mixed>
 */
class Container implements \ArrayAccess, ContainerInterface
{
    protected Resolver $resolver;

    public function __construct()
    {
        $this->resolver = Resolver::forContainer($this);
    }

    /**
     * @var array<class-string, Provider>
     */
    protected array $providers = [];

    /**
     * @var array<class-string, mixed>
     */
    protected array $container = [];

    /**
     * Register a service on the container.
     *
     * @param class-string $serviceName
     */
    public function service(string $serviceName): void
    {
        $this->factory($serviceName, function (Container $container) use ($serviceName) {
            return $container->resolver->resolve($serviceName);
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
     * ArrayAccess methods
     */

    /**
     * offsetSet
     */
    public function offsetSet($offset, $value): void
    {
        if (!is_string($offset)) {
            throw new Error\InvalidKey("Invalid key type: " . gettype($offset));
        }
        $this->container[$offset] = $value;
    }

    /**
     * offsetExists
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * offsetUnset
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * offsetGet
     */
    public function offsetGet($offset): mixed
    {
        if (!is_string($offset)) {
            throw new Error\InvalidKey("Invalid key type: " . gettype($offset));
        }
        if (isset($this->container[$offset])) {
            return $this->container[$offset];
        }

        if (isset($this->providers[$offset])) {
            $this->container[$offset] = $this->providers[$offset]($this);
        } else {
            throw new Error\OutOfBounds("No entry was found for this identifier: $offset");
        }

        return $this->container[$offset];
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

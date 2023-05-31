<?php

declare(strict_types=1);

namespace Arcanum\Cabinet;

use Psr\Container\ContainerInterface;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class Container implements \ArrayAccess, ContainerInterface
{
    /**
     * @var array<string, Provider>
     */
    protected array $providers = [];

    /**
     * @var array<string, mixed>
     */
    protected array $container = [];

    /**
     * Register an instantiated service on the container.
     */
    public function service(string $id, mixed $value): void
    {
        $this->factory($id, fn() => $value);
    }

    /**
     * Register a service factory on the container.
     */
    public function factory(string $id, \Closure $factory): void
    {
        $this->provider($id, SimpleProvider::fromFactory($factory));
    }

    /**
     * Register a service provider on the container.
     */
    public function provider(string $id, Provider $provider): void
    {
        $this->providers[$id] = $provider;
    }

    /**
     * ArrayAccess methods
     */

    public function offsetSet($offset, $value): void
    {
        if (!is_string($offset)) {
            throw new InvalidKey("Invalid key type: " . gettype($offset));
        }
        $this->container[$offset] = $value;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        if (!is_string($offset)) {
            throw new InvalidKey("Invalid key type: " . gettype($offset));
        }
        if (isset($this->container[$offset])) {
            return $this->container[$offset];
        }

        if (isset($this->providers[$offset])) {
            $this->container[$offset] = $this->providers[$offset]($this);
        } else {
            throw new OutOfBounds("No entry was found for this identifier: $offset");
        }

        return $this->container[$offset];
    }

    /**
     * ContainerInterface methods
     */

    public function get(string $id): mixed
    {
        return $this->offsetGet($id);
    }

    public function has(string $id): bool
    {
        return $this->offsetExists($id);
    }
}

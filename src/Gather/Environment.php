<?php

declare(strict_types=1);

namespace Arcanum\Gather;

/**
 * Environment is a special registry that can be used to store
 * environment variables.
 *
 * This class intentionally disallows serliazation, unserialization,
 * cloning, and string conversion, as a security measure.
 */
class Environment extends Registry
{
    /**
     * Return nothing for serialization.
     *
     * @return array<string, mixed> $data
     */
    final public function __serialize(): array
    {
        return [];
    }

    /**
     * Prevent the environment from being unserialized.
     *
     * @param array<string, mixed> $data
     */
    final public function __unserialize(array $data): void
    {
        throw new \LogicException('The environment cannot be unserialized.');
    }

    /**
     * Return "ENVIRONMENT"
     */
    final public function __toString(): string
    {
        return 'ENVIRONMENT';
    }

    /**
     * Return the data for JSON serialization.
     */
    final public function jsonSerialize(): mixed
    {
        return null;
    }

    /**
     * Prevent the environment from being cloned.
     */
    final public function __clone(): void
    {
        throw new \LogicException('The environment cannot be cloned.');
    }
}

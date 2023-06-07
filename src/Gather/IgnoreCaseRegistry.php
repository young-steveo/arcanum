<?php

declare(strict_types=1);

namespace Arcanum\Gather;

class IgnoreCaseRegistry extends Registry
{
    /**
     * Map of lowercase keys to original keys.
     *
     * @var array<string, string>
     */
    protected array $keyMap = [];

    /**
     * Construct a Registry.
     *
     * @param array<string, mixed> $data
     */
    protected function __construct(array $data)
    {
        parent::__construct($data);

        foreach (array_keys($data) as $key) {
            $this->keyMap[strtolower($key)] = $key;
        }
    }

    /**
     * Get the value stored at the given key. The key is case-insensitive.
     *
     * Will return null if the key does not exist.
     */
    public function get(string $id): mixed
    {
        $id = $this->keyMap[strtolower($id)] ?? $id;

        return parent::get($id);
    }

    /**
     * Check if the given key exists. The key is case-insensitive.
     */
    public function has(string $id): bool
    {
        $id = $this->keyMap[strtolower($id)] ?? $id;

        return parent::has($id);
    }

    /**
     * Check if the given key exists. The key is case-insensitive.
     */
    public function offsetExists(mixed $offset): bool
    {
        $offset = $this->keyMap[strtolower($offset)] ?? $offset;

        return parent::offsetExists($offset);
    }

    /**
     * Get the value stored at the given key. The key is case-insensitive.
     *
     * Will return null if the key does not exist.
     */
    public function offsetGet(mixed $offset): mixed
    {
        $offset = $this->keyMap[strtolower($offset)] ?? $offset;

        return parent::offsetGet($offset);
    }

    /**
     * Set the value at the given key. The key is case-insensitive.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $offset = $this->keyMap[strtolower((string)$offset)] ?? $offset;

        parent::offsetSet($offset, $value);
    }

    /**
     * Remove the value at the given key. The key is case-insensitive.
     */
    public function offsetUnset(mixed $offset): void
    {
        $offset = $this->keyMap[strtolower((string)$offset)] ?? $offset;

        parent::offsetUnset($offset);
    }

    /**
     * Return the value at the given key as a string. The key is
     * case-insensitive.
     */
    public function asString(string $key, string $fallback = ''): string
    {
        $key = $this->keyMap[strtolower($key)] ?? $key;

        return parent::asString($key, $fallback);
    }

    /**
     * Return the value at the given key as an integer. The key is
     * case-insensitive.
     */
    public function asInt(string $key, int $fallback = 0): int
    {
        $key = $this->keyMap[strtolower($key)] ?? $key;

        return parent::asInt($key, $fallback);
    }

    /**
     * Return the value at the given key as a float. The key is
     * case-insensitive.
     */
    public function asFloat(string $key, float $fallback = 0.0): float
    {
        $key = $this->keyMap[strtolower($key)] ?? $key;

        return parent::asFloat($key, $fallback);
    }

    /**
     * Return the value at the given key as a boolean. The key is
     * case-insensitive.
     */
    public function asBool(string $key, bool $fallback = false): bool
    {
        $key = $this->keyMap[strtolower($key)] ?? $key;

        return parent::asBool($key, $fallback);
    }
}

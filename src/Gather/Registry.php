<?php

declare(strict_types=1);

namespace Arcanum\Gather;

class Registry implements Coercible, Serializable
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        protected array $data = [],
    ) {
    }

    public function __serialize(): array
    {
        return $this->data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        $this->data = $data;
    }

    public function __toString(): string
    {
        return json_encode($this->data, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_FORCE_OBJECT);
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    public function get(string $id): mixed
    {
        return $this->data[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return isset($this->data[$id]);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    public function getAlpha(string $key, string $default = ''): string
    {
        return preg_replace('/[^a-zA-Z]/', '', $this->getString($key, $default)) ?? '';
    }

    public function getAlnum(string $key, string $default = ''): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $this->getString($key, $default)) ?? '';
    }

    public function getDigits(string $key, string $default = ''): string
    {
        return preg_replace('/[^0-9]/', '', $this->getString($key, $default)) ?? '';
    }

    public function getString(string $key, string $default = ''): string
    {
        $value = $this->data[$key] ?? $default;
        if (is_scalar($value)) {
            return (string) $value;
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }
        if (is_array($value)) {
            return json_encode($value, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_FORCE_OBJECT);
        }
        return $default;
    }

    public function getInt(string $key, int $default = 0): int
    {
        $value = $this->data[$key] ?? $default;
        if (is_scalar($value)) {
            return (int) $value;
        }
        return $default;
    }

    public function getFloat(string $key, float $default = 0.0): float
    {
        $value = $this->data[$key] ?? $default;
        if (is_scalar($value)) {
            return (float) $value;
        }
        return $default;
    }

    public function getBool(string $key, bool $default = false): bool
    {
        return (bool) ($this->data[$key] ?? $default);
    }
}

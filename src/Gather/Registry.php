<?php

declare(strict_types=1);

namespace Arcanum\Gather;

/** @phpstan-consistent-constructor */
class Registry implements Coercible, Serializable
{
    /**
     * Construct a Registry.
     *
     * @param array<string, mixed> $data
     */
    protected function __construct(
        protected array $data,
    ) {
    }

    /**
     * Construct a Registry with default data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromData(array $data): static
    {
        return new static($data);
    }

    /**
     * Construct an empty Registry.
     */
    public static function create(): static
    {
        return new static([]);
    }

    /**
     * @return array<string, mixed> $data
     */
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

    public function asAlpha(string $key, string $fallback = ''): string
    {
        return preg_replace('/[^a-zA-Z]/', '', $this->asString($key, $fallback)) ?? '';
    }

    public function asAlnum(string $key, string $fallback = ''): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $this->asString($key, $fallback)) ?? '';
    }

    public function asDigits(string $key, string $fallback = ''): string
    {
        return preg_replace('/[^0-9]/', '', $this->asString($key, $fallback)) ?? '';
    }

    public function asString(string $key, string $fallback = ''): string
    {
        $value = $this->data[$key] ?? $fallback;
        if (is_scalar($value)) {
            return (string) $value;
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }
        if (is_array($value)) {
            return json_encode($value, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_FORCE_OBJECT);
        }
        return $fallback;
    }

    public function asInt(string $key, int $fallback = 0): int
    {
        $value = $this->data[$key] ?? $fallback;
        if (is_scalar($value)) {
            return (int) $value;
        }
        return $fallback;
    }

    public function asFloat(string $key, float $fallback = 0.0): float
    {
        $value = $this->data[$key] ?? $fallback;
        if (is_scalar($value)) {
            return (float) $value;
        }
        return $fallback;
    }

    public function asBool(string $key, bool $fallback = false): bool
    {
        return (bool) ($this->data[$key] ?? $fallback);
    }
}

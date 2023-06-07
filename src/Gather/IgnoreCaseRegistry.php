<?php

declare(strict_types=1);

namespace Arcanum\Gather;

class IgnoreCaseRegistry extends Registry
{
    /**
     * @var array<string, string>
     */
    protected array $keyMap = [];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        array $data = [],
    ) {
        parent::__construct($data);

        foreach (array_keys($data) as $key) {
            $this->keyMap[strtolower($key)] = $key;
        }
    }

    public function get(string $id): mixed
    {
        $id = $this->keyMap[strtolower($id)] ?? $id;

        return parent::get($id);
    }

    public function has(string $id): bool
    {
        $id = $this->keyMap[strtolower($id)] ?? $id;

        return parent::has($id);
    }

    public function offsetExists(mixed $offset): bool
    {
        $offset = $this->keyMap[strtolower($offset)] ?? $offset;

        return parent::offsetExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $offset = $this->keyMap[strtolower($offset)] ?? $offset;

        return parent::offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $offset = $this->keyMap[strtolower((string)$offset)] ?? $offset;

        parent::offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $offset = $this->keyMap[strtolower((string)$offset)] ?? $offset;

        parent::offsetUnset($offset);
    }

    public function getString(string $key, string $default = ''): string
    {
        $key = $this->keyMap[strtolower($key)] ?? $key;

        return parent::getString($key, $default);
    }

    public function getInt(string $key, int $default = 0): int
    {
        $key = $this->keyMap[strtolower($key)] ?? $key;

        return parent::getInt($key, $default);
    }

    public function getFloat(string $key, float $default = 0.0): float
    {
        $key = $this->keyMap[strtolower($key)] ?? $key;

        return parent::getFloat($key, $default);
    }

    public function getBool(string $key, bool $default = false): bool
    {
        $key = $this->keyMap[strtolower($key)] ?? $key;

        return parent::getBool($key, $default);
    }
}

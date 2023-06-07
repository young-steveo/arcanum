<?php

declare(strict_types=1);

namespace Arcanum\Gather;

/**
 * Coercible is a Collection that can get values as a specific type.
 */
interface Coercible extends Collection
{
    public function asAlpha(string $key, string $fallback = ''): string;
    public function asAlnum(string $key, string $fallback = ''): string;
    public function asDigits(string $key, string $fallback = ''): string;
    public function asString(string $key, string $fallback = ''): string;
    public function asInt(string $key, int $fallback = 0): int;
    public function asFloat(string $key, float $fallback = 0.0): float;
    public function asBool(string $key, bool $fallback = false): bool;
}

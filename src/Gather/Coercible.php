<?php

declare(strict_types=1);

namespace Arcanum\Gather;

/**
 * Coercible is a Collection that can get values as a specific type.
 */
interface Coercible extends Collection
{
    public function getAlpha(string $key, string $default = ''): string;
    public function getAlnum(string $key, string $default = ''): string;
    public function getDigits(string $key, string $default = ''): string;
    public function getString(string $key, string $default = ''): string;
    public function getInt(string $key, int $default = 0): int;
    public function getFloat(string $key, float $default = 0.0): float;
    public function getBool(string $key, bool $default = false): bool;
}

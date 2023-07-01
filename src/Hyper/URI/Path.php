<?php

declare(strict_types=1);

namespace Arcanum\Hyper\URI;

final class Path implements \Stringable
{
    public function __construct(private string $value)
    {
        /**
         * This RegEx finds any character that is not in the unreserved set,
         * including "%", ":", "@" and "/", or any character that is not a
         * percent sign followed by two hexadecimal digits.
         *
         * If the regex successfully matches, the callback function will
         * rawurlencode the entire string.
         */
        $this->value = preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            fn(array $matches) => rawurlencode($matches[0]),
            $value
        ) ?? '';
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function contains(string $test): bool
    {
        return str_contains($this->value, $test);
    }

    public function startsWith(string $test): bool
    {
        return str_starts_with($this->value, $test);
    }
}

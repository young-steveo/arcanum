<?php

declare(strict_types=1);

namespace Arcanum\Cabinet\Error;

final class UnresolvableClass extends Unresolvable
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Unresolvable Class: $message", $code, $previous);
    }
}

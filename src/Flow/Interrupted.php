<?php

declare(strict_types=1);

namespace Arcanum\Flow;

final class Interrupted extends \Exception
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message ?: 'Flow Interrupted', $code, $previous);
    }
}

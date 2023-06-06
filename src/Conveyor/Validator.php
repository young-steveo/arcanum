<?php

declare(strict_types=1);

namespace Arcanum\Conveyor;

interface Validator
{
    /**
     * Validate an object
     */
    public function validate(object $object): void;
}

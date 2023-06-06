<?php

declare(strict_types=1);

namespace Arcanum\Conveyor;

use Arcanum\Cabinet\Container;

class StrictBus extends SwiftBus
{
    /**
     * @var Validator[]
     */
    protected array $validators;

    /**
     * StrictBus
     *
     * This bus validates that the object being dispatched passes the validators
     * and that the response from the handler passes the validators.
     */
    public function __construct(protected Container $container, Validator ...$validators)
    {
        parent::__construct($container);
        $this->validators = $validators;
    }

    /**
     * Dispatch an object to a handler.
     */
    public function dispatch(object $object): object
    {
        $this->validateObject($object);
        $response = parent::dispatch($object);
        $this->validateObject($response);
        return $response;
    }

    /**
     * Validate that an object is a simple DTO.
     *
     * A simple DTO is a class that is final, has only public, non-static,
     * read-only properties, and has no public methods.
     */
    protected function validateObject(object $object): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($object);
        }
    }
}

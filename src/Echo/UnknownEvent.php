<?php

declare(strict_types=1);

namespace Arcanum\Echo;

final class UnknownEvent extends Event
{
    private function __construct(
        public string $name,
        public object $payload,
    ) {
    }

    public static function fromObject(object $object): self
    {
        return new self(
            name: get_class($object),
            payload: $object,
        );
    }
}

<?php

declare(strict_types=1);

namespace Arcanum\Codex;

final class PrimitiveResolver
{
    public static function resolve(\ReflectionParameter $parameter): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->isVariadic()) {
            return [];
        }

        $type = $parameter->getType();
        if ($type !== null && $type instanceof \ReflectionUnionType) {
            throw new Error\UnresolvableUnionType(implode(",", $type->getTypes()));
        }

        throw new Error\UnresolvablePrimitive(message: $parameter->getName());
    }
}

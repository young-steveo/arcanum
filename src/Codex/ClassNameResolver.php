<?php

declare(strict_types=1);

namespace Arcanum\Codex;

final class ClassNameResolver
{
    /**
     * Resolve the class name of a parameter.
     *
     * @return class-string|null
     */
    public static function resolve(\ReflectionParameter $parameter): string|null
    {
        $type = $parameter->getType();

        // if it has no type, we cannot get its name.
        if ($type === null) {
            return null;
        }

        // if it is not a named type, we cannot get its name.
        if (!$type instanceof \ReflectionNamedType) {
            return null;
        }

        // if it is a built-in type, we cannot get its name.
        if ($type->isBuiltin()) {
            return null;
        }

        $name = $type->getName();

        /**
         * $class here cannot be null because we already checked
         * that it is not a built-in type.
         *
         * @var \ReflectionClass<object> $class
         */
        $class = $parameter->getDeclaringClass();

        if ($name === 'parent') {

            /**
             * $parent here cannot be false because we already checked
             * if the parent keyword is used without extending anything,
             * it would be a fatal error.
             *
             * @var \ReflectionClass<object> $parent
             */
            $parent = $class->getParentClass();
            return $parent->getName();
        }

        /** @var class-string $name */
        return $name;
    }
}

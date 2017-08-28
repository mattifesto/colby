<?php

/**
 * This class resolves the full array of required class names for an array of
 * class names. This uses an object oriented approach so that the array of
 * class names can be shared. It's a case where state is useful.
 */
final class CBRequiredClassNamesResolver {

    private $resolvedClassNames = [];

    /**
     * @return CBClassRequirementsResolver
     */
    private function __construct() { }

    /**
     * @param [string] $classNames
     *
     * @return null
     */
    private function resolve(array $classNames) {
        foreach ($classNames as $className) {
            if (in_array($className, $this->resolvedClassNames)) {
                continue;
            }

            if (is_callable($function = "{$className}::CBHTMLOutput_requiredClassNames") || is_callable($function = "{$className}::requiredClassNames")) {
                $this->resolve(call_user_func($function));
            }

            if (in_array($className, $this->resolvedClassNames)) {
                throw new RuntimeException("{$className} has a circular dependency.");
            }

            $this->resolvedClassNames[] = $className;
        }
    }

    /**
     * @param [string] $classNames
     *
     * @return [string]
     */
    static function resolveRequiredClassNames(array $classNames) {
        $resolver = new CBRequiredClassNamesResolver();
        $resolver->resolve($classNames);

        return $resolver->resolvedClassNames;
    }
}

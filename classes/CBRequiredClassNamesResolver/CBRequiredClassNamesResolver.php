<?php

/**
 * This class resolves the full array of required class names for an array of
 * class names. This uses an object oriented approach so that the array of
 * class names can be shared. It's a case where state is useful.
 */
final class CBRequiredClassNamesResolver {

    private $resolvedClassNames = [];
    private $resolvingClassNames = [];



    /**
     * @return CBClassRequirementsResolver
     */
    private function __construct() { }



    /**
     * @param [string] $classNames
     * @param [string] $getRequiredClassNamesFunctionNames
     *
     * @return void
     */
    private function
    resolve(
        array $classNames,
        array $getRequiredClassNamesFunctionNames
    ): void {
        foreach ($classNames as $className) {

            /**
             * If the class name has already been resolved we don't need to
             * resolve it again.
             */
            if (
                in_array(
                    $className,
                    $this->resolvedClassNames
                )
            ) {
                continue;
            }

            /**
             * If the class name is already in the list of currently resolving
             * class names, there is a circular dependency.
             */
            if (
                in_array(
                    $className,
                    $this->resolvingClassNames
                )
            ) {
                $dependencies  = implode(
                    ' > ',
                    $this->resolvingClassNames
                );

                throw new RuntimeException(
                    "{$className} has a circular dependency: " .
                    "{$dependencies} > {$className}"
                );
            }

            /**
             * While we resolve this class name's dependencies it will be pushed
             * onto the stack of currently resolving class names.
             */
            array_push(
                $this->resolvingClassNames,
                $className
            );

            /**
             * Resolve this class name's dependencies.
             */
            foreach(
                $getRequiredClassNamesFunctionNames as $functionName
            ) {
                $callableFunctionName = "{$className}::{$functionName}";

                if (
                    is_callable($callableFunctionName)
                ) {
                    $requiredClassNames = call_user_func(
                        $callableFunctionName
                    );

                    $this->resolve(
                        $requiredClassNames,
                        $getRequiredClassNamesFunctionNames
                    );

                    break;
                }
            }

            /**
             * We are done resolving this class name's dependencies so we can
             * pop it off the stack of currently resolving class names.
             */
            array_pop(
                $this->resolvingClassNames
            );

            /**
             * Add this class name to the list of resolved class names.
             */
            $this->resolvedClassNames[] = $className;
        }
    }
    /* resolve() */



    /**
     * @param [string] $classNames
     * @param [string] $getRequiredClassNamesFunctionNames
     *
     * @return [string]
     */
    static function
    resolveRequiredClassNames(
        array $classNames,
        array $getRequiredClassNamesFunctionNames
    ): array {
        $resolver = new CBRequiredClassNamesResolver();

        $resolver->resolve(
            $classNames,
            $getRequiredClassNamesFunctionNames
        );

        return $resolver->resolvedClassNames;
    }
    /* resolveRequiredClassNames() */

}

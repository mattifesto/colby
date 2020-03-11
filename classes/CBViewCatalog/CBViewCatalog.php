<?php

final class CBViewCatalog {

    /**
     * This variable will be set to a substitute ID to be used by
     * CBViewCatalog while tests are running.
     */
    static $testID = null;



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDB::transaction(
            function () {
                CBModels::deleteByID(
                    CBViewCatalog::ID()
                );

                CBModels::save(
                    (object)[
                        'ID' => CBViewCatalog::ID(),
                        'className' => 'CBViewCatalog',
                    ]
                );
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return array
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModels',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        return (object)[
            'viewClassNames' => CBModel::valueToArray(
                $spec,
                'viewClassNames'
            ),

            'deprecatedViewClassNames' => CBModel::valueToArray(
                $spec,
                'deprecatedViewClassNames'
            ),

            'unsupportedViewClassNames' => CBModel::valueToArray(
                $spec,
                'unsupportedViewClassNames'
            ),
        ];
    }
    /* CBModel_build() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function fetchDeprecatedViewClassNames(): array {
        $model = CBModelCache::fetchModelByID(
            CBViewCatalog::ID()
        );

        return CBModel::valueToArray(
            $model,
            'deprecatedViewClassNames'
        );
    }
    /* fetchDeprecatedViewClassNames() */



    /**
     * This function returns installed view class name that are neither
     * deprecated or unsupported.
     *
     * @return [string]
     */
    static function fetchSupportedViewClassNames(): array {
        $model = CBModelCache::fetchModelByID(
            CBViewCatalog::ID()
        );

        $supportedViewClassNames = array_diff(
            CBViewCatalog::fetchViewClassNames(),
            CBViewCatalog::fetchDeprecatedViewClassNames(),
            CBViewCatalog::fetchUnsupportedViewClassNames()
        );

        return $supportedViewClassNames;
    }
    /* fetchSupportedViewClassNames() */



    /**
     * @return [string]
     */
    static function fetchUnsupportedViewClassNames(): array {
        $model = CBModelCache::fetchModelByID(
            CBViewCatalog::ID()
        );

        return CBModel::valueToArray(
            $model,
            'unsupportedViewClassNames'
        );
    }
    /* fetchUnsupportedViewClassNames() */



    /**
     * This function returns all installed view class names: supported,
     * deprecated, and unsupported.
     *
     * @return [string]
     */
    static function fetchViewClassNames(): array {
        $model = CBModelCache::fetchModelByID(
            CBViewCatalog::ID()
        );

        return CBModel::valueToArray(
            $model,
            'viewClassNames'
        );
    }
    /* fetchViewClassNames() */



    /**
     * @return ID
     */
    static function ID(): string {
        return (
            CBViewCatalog::$testID ??
            '3d1fad418d45d081a76a027e56079d5fa464b6cc'
        );
    }
    /* ID() */



    /**
     * This function does not make any assumptions about the state of the
     * CBViewCatalog model. In reality, this function will usually be called
     * once per view class during install. But this function supports a second
     * call with different parameters, the parameters of later calls win.
     *
     * @param string $kindClassName
     * @param ?object $args
     *
     *      {
     *          isDeprecated: bool
     *
     *              Deprecated views can't be added to a page, but they can be
     *              edited, copied, and pasted. This is the warning stage that
     *              the view  will be unsupported and eventually go away in the
     *              future.
     *
     *              Deprecated view classes may have a CBModel_upgrade()
     *              function to convert specs to a another class.
     *
     *          isUnsupported: bool
     *
     *              Unsupported views can't be added to a page and most likely
     *              can't be edited either. The can be copied and pasted.
     *
     *              An unsupported view may no longer render.
     *
     *              Unsupported view classes should have a CBModel_upgrade()
     *              function that converts the view into another view.
     *
     *              If this argument is true, the isDeprecated argument will be
     *              be forced to false. A view is either suppported, deprecated,
     *              or unsupported.
     *      }
     *
     * @return void
     */
    static function installView(
        string $viewClassName,
        ?stdClass $args = null
    ): void {
        if (!class_exists($viewClassName)) {
            return;
        }

        $updater = CBModelUpdater::fetch(
            (object)[
                'className' => 'CBViewCatalog',
                'ID' => CBViewCatalog::ID(),
            ]
        );


        $workingSpec = $updater->working;

        /* view class names */

        $viewClassNames = CBModel::valueToArray(
            $workingSpec,
            'viewClassNames'
        );

        if (!in_array($viewClassName, $viewClassNames)) {
            array_push(
                $viewClassNames,
                $viewClassName
            );
        }

        $workingSpec->viewClassNames = $viewClassNames;


        /* unsupported view class names */

        $isUnsupported = CBModel::valueToBool(
            $args,
            'isUnsupported'
        );

        $unsupportedViewClassNames = CBModel::valueToArray(
            $workingSpec,
            'unsupportedViewClassNames'
        );

        if ($isUnsupported) {
            array_push(
                $unsupportedViewClassNames,
                $viewClassName
            );
        } else {
            $unsupportedViewClassNames = (
                array_values(
                    array_filter(
                        $unsupportedViewClassNames,
                        function ($currentViewClassName) use ($viewClassName) {
                            return $currentViewClassName !== $viewClassName;
                        }
                    )
                )
            );
        }

        $workingSpec->unsupportedViewClassNames = (
            array_values(
                array_unique(
                    $unsupportedViewClassNames
                )
            )
        );


        /* deprecated view class names */

        if ($isUnsupported) {
            $isDeprecated = false;
        } else {
            $isDeprecated = CBModel::valueToBool(
                $args,
                'isDeprecated'
            );
        }

        $deprecatedViewClassNames = CBModel::valueToArray(
            $workingSpec,
            'deprecatedViewClassNames'
        );

        if ($isDeprecated) {
            array_push(
                $deprecatedViewClassNames,
                $viewClassName
            );
        } else {
            $deprecatedViewClassNames = (
                array_values(
                    array_filter(
                        $deprecatedViewClassNames,
                        function ($currentViewClassName) use ($viewClassName) {
                            return $currentViewClassName !== $viewClassName;
                        }
                    )
                )
            );
        }

        $workingSpec->deprecatedViewClassNames = (
            array_values(
                array_unique(
                    $deprecatedViewClassNames
                )
            )
        );

        CBModelUpdater::save($updater);
    }
    /* installView() */

}

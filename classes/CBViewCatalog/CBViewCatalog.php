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
        CBModelUpdater::update(
            (object)[
                'ID' => CBViewCatalog::ID(),
                'className' => 'CBViewCatalog',
                'viewClassNames' => [],
                'deprecatedViewClassNames' => [],
                'unsupportedViewClassNames' => [],
            ]
        );
    }

    /**
     * @return array
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModelUpdater',
        ];
    }

    /* -- CBModel interfaces -- -- -- -- -- */

    /**
     * @param model $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'viewClassNames' => CBModel::valueToArray($spec, 'viewClassNames'),
            'deprecatedViewClassNames' => CBModel::valueToArray($spec, 'deprecatedViewClassNames'),
            'unsupportedViewClassNames' => CBModel::valueToArray($spec, 'unsupportedViewClassNames'),
        ];
    }

    /* -- functions -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function fetchDeprecatedViewClassNames(): array {
        $model = CBModelCache::fetchModelByID(CBViewCatalog::ID());

        return CBModel::valueToArray($model, 'deprecatedViewClassNames');
    }

    /**
     * @return [string]
     */
    static function fetchViewClassNames(): array {
        $model = CBModelCache::fetchModelByID(CBViewCatalog::ID());

        return CBModel::valueToArray($model, 'viewClassNames');
    }

    /**
     * @return [string]
     */
    static function fetchUnsupportedViewClassNames(): array {
        $model = CBModelCache::fetchModelByID(CBViewCatalog::ID());

        return CBModel::valueToArray($model, 'unsupportedViewClassNames');
    }

    /**
     * @return ID
     */
    static function ID(): string {
        return CBViewCatalog::$testID ??
            '3d1fad418d45d081a76a027e56079d5fa464b6cc';
    }

    /**
     * @param string $kindClassName
     * @param ?object $args
     *
     *      {
     *          isDeprecated: bool
     *
     *              A deprecated view is a view that should not be used but
     *              still functions properly. It will become unsupported in a
     *              future release and in a subsequent release removed entirely.
     *              Deprecated view classes may have a CBModel_upgrade()
     *              function to convert specs to a another class.
     *
     *          isUnsupported: bool
     *
     *              An unsupported view is one which no longer functions and
     *              will be removed entirely in the future. Unsupported view
     *              classes will often have CBModel_upgrade() functions that
     *              convert specs to another class.
     *
     *              If this argument is true, the isDeprecated argument will be
     *              ignored.
     *      }
     *
     * @return void
     */
    static function installView(string $viewClassName, ?stdClass $args = null): void {
        if (!class_exists($viewClassName)) {
            return;
        }

        $originalSpec = CBModels::fetchSpecByID(CBViewCatalog::ID());

        if (empty($originalSpec)) {
            $originalSpec = (object)[
                'ID' => CBViewCatalog::ID(),
            ];
        }

        $spec = CBModel::clone($originalSpec);
        $spec->className = 'CBViewCatalog';
        $viewClassNames = CBModel::valueToArray($spec, 'viewClassNames');

        array_push($viewClassNames, $viewClassName);

        $spec->viewClassNames = array_values(array_filter(array_unique(
            $viewClassNames
        )));

        if (CBModel::valueToBool($args, 'isUnsupported')) {
            $unsupportedViewClassNames = CBModel::valueToArray($spec, 'unsupportedViewClassNames');

            array_push($unsupportedViewClassNames, $viewClassName);

            $spec->unsupportedViewClassNames = array_values(array_filter(array_unique(
                $unsupportedViewClassNames
            )));
        } else if (CBModel::valueToBool($args, 'isDeprecated')) {
            $deprecatedViewClassNames = CBModel::valueToArray($spec, 'deprecatedViewClassNames');

            array_push($deprecatedViewClassNames, $viewClassName);

            $spec->deprecatedViewClassNames = array_values(array_filter(array_unique(
                $deprecatedViewClassNames
            )));
        }

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }
}

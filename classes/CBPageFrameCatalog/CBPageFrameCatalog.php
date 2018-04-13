<?php

final class CBPageFrameCatalog {

    /**
     * This variable will be set to a substitute ID to be used by
     * CBPageFrameCatalog while tests are running.
     */
    static $testID = null;

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDB::transaction(function () {
            CBModels::deleteByID(CBPageFrameCatalog::ID());
        });
    }

    /**
     * @return array
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBModels'];
    }

    /**
     * @param model $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'classNames' => CBModel::valueToArray($spec, 'classNames'),
        ];
    }

    /**
     * @return [string]
     */
    static function fetchClassNames(): array {
        $model = CBModels::fetchModelByID(CBPageFrameCatalog::ID());

        return CBModel::valueToArray($model, 'classNames');
    }


    /**
     * @return ID
     */
    static function ID(): string {
        return CBPageFrameCatalog::$testID ??
            'ba9966cc7bdc57747fbe07f684dd7097e04b1aa6';
    }

    /**
     * @param string $frameClassName
     *
     * @return void
     */
    static function install(string $frameClassName): void {
        if (!is_callable("{$frameClassName}::CBPageFrame_render")) {
            return;
        }

        $originalSpec = CBModels::fetchSpecByID(CBPageFrameCatalog::ID());

        if (empty($originalSpec)) {
            $originalSpec = (object)[
                'ID' => CBPageFrameCatalog::ID(),
            ];
        }

        $spec = CBModel::clone($originalSpec);
        $spec->className = 'CBPageFrameCatalog';
        $frameClassNames = CBModel::valueToArray($spec, 'classNames');

        array_push($frameClassNames, $frameClassName);

        $spec->classNames = array_values(array_filter(array_unique(
            $frameClassNames
        )));

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }
}

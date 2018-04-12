<?php

final class CBPageSettingsCatalog {

    /**
     * This variable will be set to a substitute ID to be used by
     * CBPageSettingsCatalog while tests are running.
     */
    static $testID = null;

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDB::transaction(function () {
            CBModels::deleteByID(CBPageSettingsCatalog::ID());
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
        $model = CBModels::fetchModelByID(CBPageSettingsCatalog::ID());

        return CBModel::valueToArray($model, 'classNames');
    }


    /**
     * @return ID
     */
    static function ID(): string {
        return CBPageSettingsCatalog::$testID ??
            'b96ba34dd5b2861427260cc8f7c0719bc741be17';
    }

    /**
     * @param string $pageSettingsClassName
     *
     * @return void
     */
    static function install(string $pageSettingsClassName): void {
        if (!class_exists($pageSettingsClassName)) {
            return;
        }

        $originalSpec = CBModels::fetchSpecByID(CBPageSettingsCatalog::ID());

        if (empty($originalSpec)) {
            $originalSpec = (object)[
                'ID' => CBPageSettingsCatalog::ID(),
            ];
        }

        $spec = CBModel::clone($originalSpec);
        $spec->className = 'CBPageSettingsCatalog';
        $pageSettingsClassNames = CBModel::valueToArray($spec, 'classNames');

        array_push($pageSettingsClassNames, $pageSettingsClassName);

        $spec->classNames = array_values(array_filter(array_unique(
            $pageSettingsClassNames
        )));

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }
}

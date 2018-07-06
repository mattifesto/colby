<?php

final class CBPageKindCatalog {

    /**
     * This variable will be set to a substitute ID to be used by
     * CBPageKindCatalog while tests are running.
     */
    static $testID = null;

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDB::transaction(function () {
            CBModels::deleteByID(CBPageKindCatalog::ID());
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
        $model = CBModels::fetchModelByID(CBPageKindCatalog::ID());

        return CBModel::valueToArray($model, 'classNames');
    }


    /**
     * @return ID
     */
    static function ID(): string {
        return CBPageKindCatalog::$testID ??
            '7f67ce7bacba3a14489e50905b1abfba9333a9d2';
    }

    /**
     * @param string $kindClassName
     *
     * @return void
     */
    static function install(string $kindClassName): void {
        if (!class_exists($kindClassName)) {
            return;
        }

        $originalSpec = CBModels::fetchSpecByID(CBPageKindCatalog::ID());

        if (empty($originalSpec)) {
            $spec = (object)[
                'ID' => CBPageKindCatalog::ID(),
            ];
        } else {
            $spec = CBModel::clone($originalSpec);
        }

        $spec->className = 'CBPageKindCatalog';
        $kindClassNames = CBModel::valueToArray($spec, 'classNames');

        array_push($kindClassNames, $kindClassName);

        $spec->classNames = array_values(array_filter(array_unique(
            $kindClassNames
        )));

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }
}

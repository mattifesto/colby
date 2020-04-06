<?php

final class SCCountry {

    /**
     * @param object $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'isActive' => CBModel::valueToBool($spec, 'isActive'),
            'isDefault' => CBModel::valueToBool($spec, 'isDefault'),
            'moniker' => CBModel::valueAsMoniker($spec, 'moniker'),
            'title' => trim(CBModel::valueToString($spec, 'title')),
        ];
    }

    /**
     * @param object $spec
     *
     * @return ?ID
     */
    static function CBModel_toID(stdClass $spec): ?string {
        $moniker = CBModel::valueAsMoniker($spec, 'moniker');

        if ($moniker === null) {
            return null;
        } else {
            return SCCountry::monikerToID($moniker);
        }
    }

    /**
     * @param [ID] $modelIDs
     *
     * @return void
     */
    static function CBModels_willDelete(array $modelIDs): void {
        CBTasks2::restart('SCCountryUpdateTask', $modelIDs);
    }

    /**
     * @param [model] $models
     *
     * @return void
     */
    static function CBModels_willSave(array $models): void {
        $modelIDs = array_map(
            function ($model) {
                return $model->ID;
            },
            $models
        );

        CBTasks2::restart('SCCountryUpdateTask', $modelIDs);
    }

    /**
     * @param string $moniker
     *
     * @return ID
     */
    static function monikerToID(string $moniker): string {
        return sha1("96e6dabb16069a8d53fd66f58430c6ac8e0f7a40 {$moniker}");
    }
}

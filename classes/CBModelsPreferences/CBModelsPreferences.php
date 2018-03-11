<?php

final class CBModelsPreferences {

    const ID = '69b3958b95e87cca628fc2b9cd70f420faf33a0a';
    const defaultClassNamesOfEditableModels = [
        'CBMenu',
        'CBTheme',
        'CBViewPage',
        'CBSitePreferences',
        'CBModelsPreferences',
        'CBPagesPreferences'
    ];

    /**
     * @return [string]
     */
    static function classNamesOfEditableModels(): array {
        $model = CBModelCache::fetchModelByID(CBModelsPreferences::ID);
        $classNamesOfEditableModels = CBModel::valueToArray($model, 'classNamesOfEditableModels');

        return array_merge(CBModelsPreferences::defaultClassNamesOfEditableModels, $classNamesOfEditableModels);
    }

    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'classNamesOfEditableModels' => array_values(array_unique(
                CBModel::valueToNames($spec, 'classNamesOfEditableModels')
            )),
        ];
    }
}

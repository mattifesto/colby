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
    static function classNamesOfEditableModels() {
        $model = CBModelCache::fetchModelByID(CBModelsPreferences::ID);
        return array_merge(CBModelsPreferences::defaultClassNamesOfEditableModels, $model->classNamesOfEditableModels);
    }

    /**
     * Re-saving these preferences each update ensures that the model always has
     * valid values for all properties without having to add a new update script
     * each time the properties change.
     *
     * @return null
     */
    static function install() {
        $spec = CBModels::fetchSpecByID(CBModelsPreferences::ID);

        if ($spec === false) {
            $spec = CBModels::modelWithClassName(__CLASS__, ['ID' => CBModelsPreferences::ID]);
        }

        CBModels::save([$spec]);
    }

    /**
     * @return stdClass
     */
    static function CBModel_toModel(stdClass $spec) {
        $model = CBModels::modelWithClassName(__CLASS__);

        if (isset($spec->classNamesOfEditableModels)) {
            $classes = preg_split('/[,\s]+/', $spec->classNamesOfEditableModels);
            $model->classNamesOfEditableModels = array_unique($classes);
        } else {
            $model->classNamesOfEditableModels = [];
        }

        return $model;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}

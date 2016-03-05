<?php

final class CBModelsPreferences {

    const ID = '69b3958b95e87cca628fc2b9cd70f420faf33a0a';
    const defaultClassNamesOfEditableModels = [
        'CBMenu',
        'CBTextBoxViewTheme',
        'CBTheme',
        'CBSitePreferences',
        'CBModelsPreferences',
        'CBPagesPreferences'
    ];

    /**
     * @return [string]
     */
    public static function classNamesOfEditableModels() {
        $model = CBModelCache::fetchModelByID(CBModelsPreferences::ID);
        return array_merge(CBModelsPreferences::defaultClassNamesOfEditableModels, $model->classNamesOfEditableModels);
    }

    /**
     * @return [string]
     */
    public static function editorURLsForCSS() {
        return [
            CBModelsPreferences::URL('CBModelsPreferencesEditor.css')
        ];
    }

    /**
     * @return [string]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBResponsiveEditorFactory.js',
            CBModelsPreferences::URL('CBModelsPreferencesEditorFactory.js'),
        ];
    }

    /**
     * @return stdClass
     */
    public static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Models Preferences',
            'singularTitle' => 'Models Preferences',
        ]);
    }

    /**
     * Re-saving these preferences each update ensures that the model always has
     * valid values for all properties without having to add a new update script
     * each time the properties change.
     *
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBModelsPreferences::ID);

        if ($spec === false) {
            $spec = CBModels::modelWithClassName(__CLASS__, ['ID' => CBModelsPreferences::ID]);
        }

        CBModels::save([$spec]);
    }

    /**
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
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
     * @return string
     */
    public static function URL($filename) {
        $class = __CLASS__;
        return CBSystemURL . "/classes/{$class}/{$filename}";
    }
}

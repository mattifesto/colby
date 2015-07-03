<?php

final class CBPagesPreferences {

    const ID = '3ff6fabd8a0da44f1b2d5f5faee6961af8e5a9df';

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [ CBPagesPreferences::URL('CBPagesPreferencesEditor.css') ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBPagesPreferences::URL('CBPagesPreferencesEditorFactory.js')
        ];
    }

    /**
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBPagesPreferences::ID);

        if ($spec === false) {
            $spec = CBModels::modelWithClassName(__CLASS__, [ 'ID' => CBPagesPreferences::ID ]);
            $spec->supportedViewClassNames = 'CBBackgroundView CBImageLinkView CBTextBoxView';
            CBModels::save([$spec]);
        }
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                              = CBModels::modelWithClassName(__CLASS__);
        $model->supportedViewClassNames     = [];
        $model->selectableViewClassNames    = [];

        if (isset($spec->supportedViewClassNames)) {
            $model->supportedViewClassNames = preg_split('/[\s]+/', $spec->supportedViewClassNames);

            array_walk($model->supportedViewClassNames, function($className) {
                if (!class_exists($className)) {
                    throw new Exception("The view class \"{$className}\" is not installed.");
                }
            });
        }

        if (isset($spec->deprecatedViewClassNames)) {
            $deprecatedViewClassNames           = preg_split('/[\s]+/', $spec->deprecatedViewClassNames);
            $model->selectableViewClassNames    = array_diff($model->supportedViewClassNames, $deprecatedViewClassNames);
        } else {
            $model->selectableViewClassNames    = $model->supportedViewClassNames;
        }

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBPagesPreferences/{$filename}";
    }
}

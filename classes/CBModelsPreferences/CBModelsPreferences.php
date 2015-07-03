<?php

final class CBModelsPreferences {

    const ID = '69b3958b95e87cca628fc2b9cd70f420faf33a0a';

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return array_merge([
                CBSystemURL . '/javascript/CBSpecArrayEditor.css',
                CBModelsPreferences::URL('CBModelsPreferencesEditor.css')
            ],
            CBClassMenuItem::editorURLsForCSS()
        );
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return array_merge([
                CBSystemURL . '/javascript/CBSpecArrayEditorFactory.js',
                CBSystemURL . '/javascript/CBStringEditorFactory.js',
                CBModelsPreferences::URL('CBModelsPreferencesEditorFactory.js')
            ],
            CBClassMenuItem::editorURLsForJavaScript()
        );
    }

    /**
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBModelsPreferences::ID);

        if ($spec === false) {
            $spec = CBModels::modelWithClassName(__CLASS__, [ 'ID' => CBModelsPreferences::ID ]);

            $menuItem                   = CBModels::modelWithClassName('CBClassMenuItem');
            $menuItem->itemClassName    = 'CBMenu';
            $menuItem->group            = 'Developers';
            $menuItem->title            = 'Menus';
            $spec->classMenuItems[]     = $menuItem;

            $menuItem                   = CBModels::modelWithClassName('CBClassMenuItem');
            $menuItem->itemClassName    = 'CBTextBoxTheme';
            $menuItem->group            = 'Developers';
            $menuItem->title            = 'Text Box Themes';
            $spec->classMenuItems[]     = $menuItem;

            CBModels::save([$spec]);
        }
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                  = CBModels::modelWithClassName(__CLASS__);
        $model->classMenuItems  = array_map('CBClassMenuItem::specToModel', $spec->classMenuItems);

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBModelsPreferences/{$filename}";
    }
}

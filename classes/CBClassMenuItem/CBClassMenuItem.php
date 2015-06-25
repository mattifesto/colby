<?php

/**
 * This class represents a single menu item in a menu of class names such as a
 * menu of class names of views in the page editor or a menu of class names of
 * editable model classes in the model editor.
 *
 * Whenever you are creating a menu of class names, this is the class to use to
 * represent a single menu item.
 */
final class CBClassMenuItem {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [
            CBClassMenuItem::URL('CBClassMenuItemEditor.css')
        ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBClassMenuItem::URL('CBClassMenuItemEditorFactory.js')
        ];
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                  = CBModels::modelWithClassName(__CLASS__);
        $model->itemClassName   = isset($spec->itemClassName) ? (string)$spec->itemClassName : '';
        $model->group           = isset($spec->group) ? (string)$spec->group : '';
        $model->title           = isset($spec->title) ? (string)$spec->title : '';

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBClassMenuItem/{$filename}";
    }
}

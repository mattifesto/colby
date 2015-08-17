<?php

final class CBPageKindView {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [
            CBPageKindView::URL('CBPageKindViewEditor.css')
        ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBPageKindView::URL('CBPageKindViewEditorFactory.js')
        ];
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        if ($model->classNameForKind === null) {
            echo '<!-- CBPageKindView: You must set a page kind for this view to work propertly. -->';
            return;
        }

        $type = isset($_GET['cbpagekindviewtype']) ? $_GET['cbpagekindviewtype'] : null;

        switch ($type) {
            case 'catalog':
                break;

            case 'catalogformonth':
                break;

            default:
                include __DIR__ . '/renderAsMostRecentlyPublishedPages.php';
        }
    }

    /**
     * @return  {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                      = CBModels::modelWithClassName(__CLASS__);
        $model->classNameForKind    = isset($spec->classNameForKind) ? $spec->classNameForKind : null;

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBPageKindView/{$filename}";
    }
}

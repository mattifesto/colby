<?php

final class CBTextBoxView {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [ CBTextBoxView::URL('CBTextBoxViewEditor.css') ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBTextBoxView::URL('CBTextBoxViewEditorFactory.js')
        ];
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                      = CBModels::modelWithClassName(__CLASS__);
        $model->title               = isset($spec->title) ? $spec->title : '';
        $model->titleAsHTML         = ColbyConvert::textToHTML($model->title);
        $model->contentAsMarkaround = isset($spec->contentAsMarkaround) ? $spec->contentAsMarkaround : '';
        $model->contentAsHTML       = CBMarkaround::textToHTML(['text' => $model->contentAsMarkaround]);

        return $model;
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        ?>

        <section>
            <h1><?= $model->titleAsHTML ?></h1>
            <div><?= $model->contentAsHTML ?></div>
        </section>

        <?php
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBTextBoxView/{$filename}";
    }
}

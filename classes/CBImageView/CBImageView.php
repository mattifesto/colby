<?php

final class CBImageView {

    /**
     * @return void
     */
    public static function includeEditorDependencies() {
        CBHTMLOutput::addCSSURL(self::URL('CBImageViewEditor.css'));
        CBHTMLOutput::addJavaScriptURL(self::URL('CBImageViewEditorFactory.js'));
    }

    /**
     * @return bool
     */
    public static function modelHasImage(stdClass $model = null) {
        return !!$model->filename;
    }

    /**
     * @return string
     */
    public static function modelToSearchText(stdClass $model = null) {
        $altTextModel = isset($model->alternativeTextViewModel) ? $model->alternativeTextViewModel : null;

        return CBTextView::modelToSearchText($altTextModel);
    }

    /**
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        $styles = array();

        if ($model->displayHeight || $model->displayWidth) {

            if ($model->displayHeight) {

                $styles[] = "height: {$model->displayHeight}px;";
            }

            if ($model->displayWidth) {

                $styles[] = "width: {$model->displayWidth}px;";
            }

        } else if ($model->maxHeight || $model->maxWidth) {

            if ($model->maxHeight) {

                $styles[] = "max-height: {$model->maxHeight}px;";
            }

            if ($model->maxWidth) {

                $styles[] = "max-width: {$model->maxWidth}px;";
            }

        } else {

            $styles[] = "height: {$model->actualHeight}px;";
            $styles[] = "width: {$model->actualWidth}px;";
        }

        $styles = implode(' ', $styles);

        ?>

        <img alt="<?= $model->alternativeTextViewModel->HTML ?>"
             src="<?= $model->URLForHTML ?>"
             style="<?= $styles ?>">

        <?php
    }

    /**
     * @return stdClass
     */
    public static function specToModel(stdClass $spec = null) {
        $model                  = CBView::modelWithClassName(__CLASS__);
        $model->actualHeight    = isset($spec->actualHeight) ? $spec->actualHeight : null;
        $model->actualWidth     = isset($spec->actualWidth) ? $spec->actualWidth : null;
        $model->displayHeight   = isset($spec->displayHeight) ? $spec->displayHeight : null;
        $model->displayWidth    = isset($spec->displayWidth) ? $spec->displayWidth : null;
        $model->filename        = isset($spec->filename) ? $spec->filename : null;
        $model->maxHeight       = isset($spec->maxHeight) ? $spec->maxHeight : null;
        $model->maxWidth        = isset($spec->maxWidth) ? $spec->maxWidth : null;
        $model->URL             = isset($spec->URL) ? $spec->URL : null;
        $model->URLForHTML      = ColbyConvert::textToHTML($model->URL);
        $altTextSpec            = isset($spec->alternativeTextViewModel) ? $spec->alternativeTextViewModel : null;

        $model->alternativeTextViewModel = CBTextView::specToModel($altTextSpec);

        return $model;
    }

    /**
     * @return string
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBImageView/{$filename}";
    }
}

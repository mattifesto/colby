<?php

/**
 * @deprecated 2015.06.09
 *      This class has been around since very early on in the process of
 *  creating views. Because of this, the theory behind views was not fully
 *  formed and it is not elegantly implemented.
 *      It is currently used by the MCLinkView and the LEMiniLinkView. These
 *  views should be deprecated also and replaced by views that behave better.
 *  When there is system wide view upgrade functionality all of the models can
 *  be upgraded and these views deleted.
 */
final class CBImageView {

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
            if ($model->actualHeight) {
                $styles[] = "height: {$model->actualHeight}px;";
            }

            if ($model->actualWidth) {
                $styles[] = "width: {$model->actualWidth}px;";
            }
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
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}

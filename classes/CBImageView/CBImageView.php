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
     * @param object $model
     *
     * @return bool
     */
    static function modelHasImage(stdClass $model) {
        return !!$model->filename;
    }

    /**
     * @param object $model
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model) {
        return CBModel::value($model, 'alternativeTextViewModel.text', '', 'trim');
    }

    /**
     * @param object $model
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
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
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
        ];
        $model->actualHeight = isset($spec->actualHeight) ? $spec->actualHeight : null;
        $model->actualWidth = isset($spec->actualWidth) ? $spec->actualWidth : null;
        $model->displayHeight = isset($spec->displayHeight) ? $spec->displayHeight : null;
        $model->displayWidth = isset($spec->displayWidth) ? $spec->displayWidth : null;
        $model->filename = isset($spec->filename) ? $spec->filename : null;
        $model->maxHeight = isset($spec->maxHeight) ? $spec->maxHeight : null;
        $model->maxWidth = isset($spec->maxWidth) ? $spec->maxWidth : null;
        $model->URL = isset($spec->URL) ? $spec->URL : null;
        $model->URLForHTML = ColbyConvert::textToHTML($model->URL);
        $altTextSpec = isset($spec->alternativeTextViewModel) ? $spec->alternativeTextViewModel : null;

        $textViewSpecToModel = function (stdClass $spec = null) {
            $model = (object)[];
            $model->text = isset($spec->text) ? (string)$spec->text : '';
            $model->HTML = ColbyConvert::textToHTML($model->text);

            return $model;
        };

        $model->alternativeTextViewModel = $textViewSpecToModel($altTextSpec);

        return $model;
    }
}

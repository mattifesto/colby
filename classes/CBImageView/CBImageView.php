<?php

final class CBImageView {

    /**
     * @return instance type
     */
    public static function init() {
        $view                                   = new self();
        $view->alternativeTextView              = CBTextView::init();
        $view->model                            = CBView::modelWithClassName(__CLASS__);
        $view->model->actualHeight              = null;
        $view->model->actualWidth               = null;
        $view->model->alternativeTextViewModel  = $view->alternativeTextView->model;
        $view->model->displayHeight             = null;
        $view->model->displayWidth              = null;
        $view->model->filename                  = null;
        $view->model->maxHeight                 = null;
        $view->model->maxWidth                  = null;
        $view->model->URL                       = null;
        $view->model->URLForHTML                = null;

        return $view;
    }

    /**
     * @return instance type
     */
    public static function initWithModel($model) {
        $view                       = new self();
        $view->model                = $model;
        $view->alternativeTextView  = CBTextView::initWithModel($view->model->alternativeTextViewModel);

        return $view;
    }

    /**
     * @return void
     */
    public static function includeEditorDependencies() {
        CBView::includeEditorDependencies();
        CBTextView::includeEditorDependencies();

        $URL = CBSystemURL . '/classes/CBImageView/CBImageViewEditor.css';
        CBHTMLOutput::addCSSURL($URL);

        $URL = CBSystemURL . '/classes/CBImageView/CBImageViewEditor.js';
        CBHTMLOutput::addJavaScriptURL($URL);
    }

    /**
     * @return bool
     */
    public function hasImage() {
        return self::modelHasImage($this->model);
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
    public function renderHTML() {

        $model  = $this->model;
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

        <img alt="<?= $this->alternativeTextView->HTML() ?>"
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
        $model->URLForHTML      = isset($spec->URLForHTML) ? $spec->URLForHTML : null;
        $altTextSpec            = isset($spec->alternativeTextViewModel) ? $spec->alternativeTextViewModel : null;

        $model->alternativeTextViewModel = CBTextView::specToModel($altTextSpec);

        return $model;
    }
}

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

        return !!$this->model->filename;
    }

    /**
     * @return string
     */
    public static function modelToSearchText(stdClass $model = null) {
        if (isset($model->alternativeTextViewModel)) {
            return CBView::modelToSearchText($model->alternativeTextViewModel);
        }

        return '';
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
}

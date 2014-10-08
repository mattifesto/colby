<?php

class CBImageView extends CBView {

    /**
     * @return instance type
     */
    public static function init() {

        $view                                   = parent::init();
        $view->alternativeTextView              = CBTextView::init();
        $view->model->actualHeight              = null;
        $view->model->actualWidth               = null;
        $view->model->alternativeTextViewModel  = $view->textView->model();
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

        $view                       = parent::initWithModel($model);
        $view->alternativeTextView  = CBTextView::initWithModel($view->model->alternativeTextViewModel);

        return $view;
    }

    /**
     * @return void
     */
    public static function includeEditorDependencies() {

        parent::includeEditorDependencies();
        CBTextView::includeEditorDependencies();

        $URL = CBSystemURL . '/classes/CBImageView/CBImageViewEditor.css';

        CBHTMLOutput::addCSSURL($URL);

        $URL = CBSystemURL . '/classes/CBImageView/CBImageViewEditor.js';

        CBHTMLOutput::addJavaScriptURL($URL);
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
     * @return string
     */
    public function searchText() {

        return $this->alternativeTextView->searchText();
    }

}
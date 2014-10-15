<?php


/**
 *
 */
class CBTextView extends CBView {

    const contentTypeSingleLinePlainText        = 0;
    const contentTypeSingleLineFormattedText    = 1;
    const contentTypeMultiLinePlainText         = 2;
    const contentTypeMultiLineFormattedText     = 3;
    const contentTypeMultiLineMarkaround        = 4;

    /**
     * @return instance type
     */
    public static function init() {

        $view = parent::init();

        $view->model->text          = '';
        $view->model->HTML          = '';
        $view->model->contentType   = self::contentTypeSingleLinePlainText;

        return $view;
    }

    /**
     * 2014.10.14
     *  The `contentType` property was added to the model so this method now
     *  sets it if it to the default value if it doesn't already exists.
     *  This value may not be correct for all existing models but it will
     *  produce the current behavior regardless. The number of models existing
     *  in the wild is very low.
     *
     * @return instance type
     */
    public static function initWithModel($model) {

        $view = parent::initWithModel($model);

        if (!isset($model->contentType)) {

            $model->contentType = self::contentTypeSingleLinePlainText;
        }

        return $view;
    }

    /**
     * @return void
     */
    public function setContentType($contentType) {

        $this->model->contentType = (int)$contentType;

        $this->setText($this->model->text);
    }

    /**
     * @return string
     */
    public function HTML() {

        return $this->model->HTML;
    }

    /**
     * @return void
     */
    public static function includeEditorDependencies() {

        parent::includeEditorDependencies();

        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBMarkaround.js');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBTextView/CBTextViewEditor.js');
    }

    /**
     * @return void
     */
    public function renderHTML() {

        $className = get_class($this);

        if ($this->model->contentType == self::contentTypeSingleLinePlainText ||
            $this->model->contentType == self::contentTypeSingleLineFormattedText) {

            $tagName = 'span';

        } else {

            $tagName = 'div';
        }

        echo "<{$tagName} class=\"{$className}\">{$this->model->HTML}</{$tagName}>";
    }

    /**
     * @return string
     */
    public function searchText() {

        return $this->model->text;
    }

    /**
     * @return void
     */
    public function setText($text) {

        /**
         * 2014.10.14 TODO:
         *  This should do different things based on the content type.
         */
        $this->model->text  = (string)$text;
        $this->model->HTML  = ColbyConvert::textToHTML($this->model->text);
    }

    /**
     * @return string
     */
    public function text() {

        return $this->model->text;
    }
}

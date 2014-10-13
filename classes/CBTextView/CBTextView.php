<?php

/**
 *
 */
class CBTextView extends CBView {

    /**
     * @return instance type
     */
    public static function init() {

        $view = parent::init();

        $view->model->text  = '';
        $view->model->HTML  = '';

        return $view;
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

        echo "<span class=\"{$className}\">{$this->model->HTML}</span>";
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

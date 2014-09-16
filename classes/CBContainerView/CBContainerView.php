<?php

class CBContainerView extends CBView {

    /**
     * @return instance type
     */
    public static function init() {

        $view   = parent::init();
        $model  = $view->model;

        $model->subviews = array();

        return $view;
    }

    /**
     * @return void
     */
    public function addSubview($view) {

        $this->model->subviews[] = $view;
    }

    /**
     * @return void
     */
    public function renderHTML() {

        include __DIR__ . '/CBContainerViewHTML.php';
    }
}

<?php

class CBView {

    protected $model;

    /**
     * @return instance type
     */
    private function __construct() { }

    /**
     * This method is called to create a new view. Override this method to add
     * custom properties to the model and to do additional initialization.
     *
     * Overrides must call this function to get the instance.
     *
     * @return instance type
     */
    public static function init() {

        $model              = new stdClass();
        $model->className   = get_called_class();
        $model->ID          = Colby::random160();
        $model->version     = 1;

        $view               = new static();
        $view->model        = $model;

        return $view;
    }

    /**
     * This method is called when a model already exists for a view. Override
     * this method if you need to do additional initialization aside from just
     * storing the model.
     *
     * Overrides must call this function to get the instance.
     *
     * @return instance type
     */
    public static function initWithModel($model) {

        if (get_called_class() != $model->className)
        {
            throw new InvalidArgumentException('The model provided is not a model for this view class.');
        }

        $view           = new static();
        $view->model    = $model;

        return $view;
    }

    /**
     * This function creates the a new view of the correct type from any view
     * model. It would be used when enumerating through views to render them or
     * perform other non-type specific tasks.
     *
     * @return CBView
     */
    final public static function createViewWithModel($model) {

        $className  = $model->className;
        $view       = $className::initWithModel($model);

        return $view;
    }

    /**
     * This method is implemented to provide working but not useful
     * functionality for new views to make creating new views simple. It should
     * eventually be overridden by the subclass.
     *
     * @return void
     */
    public static function includeEditorDependencies() {

        CBHTMLOutput::addCSSURL(CBSystemURL . '/classes/CBView/CBViewEditor.css');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBView/CBViewEditor.js');
    }

    /**
     * @return object
     */
    public function model() {

        return $this->model;
    }

    /**
     * @return void
     */
    public function renderHTML() {

        $className = get_class($this);

        echo "\n\n<!-- This is the default output for a `{$className}` class. -->\n\n";
    }

    /**
     * @return void
     */
    public function searchText() {

        return '';
    }
}

<?php

class CBView
{
    protected $model;

    /**
     * @return instance type
     */
    private function __construct()
    {
    }

    /**
     * @return instance type
     */
    public static function init()
    {
        $model              = new stdClass();
        $model->className   = get_called_class();
        $model->ID          = Colby::random160();
        $model->version     = 1;

        return self::initWithModel($model);
    }

    /**
     * This is the designated initializer. This is the only function that
     * creates an instance of the class.
     *
     * @param object $model
     *
     *  We trust that the object passed here is an appropriate model for this
     *  view which was created by another instance of this class.
     *
     * @return instance type
     */
    public static function initWithModel($model)
    {
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
    public static function createViewWithModel($model)
    {
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
    public static function includeEditorDependencies()
    {
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBView/CBViewEditor.js');

        $viewClassName      = get_called_class();
        $editorClassName    = "{$viewClassName}Editor";
        $snippet            = <<<EOT

            var {$editorClassName} = Object.create(CBViewEditor);

            {$editorClassName}.init = function()
            {
                CBViewEditor.init.call(this);

                this.model.className = "{$viewClassName}";

                return this;
            };

EOT;

        CBHTMLOutput::addJavaScriptSnippetString($snippet);
    }

    /**
     * @return object
     */
    public function model()
    {
        return $model;
    }

    /**
     * @return void
     */
    public function renderHTML()
    {
        $className = get_class($this);

        echo "\n\n<!-- This is the default output for a `{$className}` class. -->\n\n";
    }

    /**
     * @return void
     */
    public function searchText()
    {
        return '';
    }
}

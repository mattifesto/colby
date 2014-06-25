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
     * @return void
     */
    public static function includeEditorDependencies()
    {
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
    }
}

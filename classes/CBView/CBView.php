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
        $model = new stdClass();

        /**
         * TODO: set default model properties here
         */

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

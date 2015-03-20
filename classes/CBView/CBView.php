<?php

final class CBView {

    public $model;

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

        $modelIsRecognized  = isset($model->className) && class_exists($model->className);
        $upgraderDoesExist  = class_exists('CBViewModelUpgrader');

        if (!$modelIsRecognized && $upgraderDoesExist) {

            CBViewModelUpgrader::upgradeModel($model);

            $modelIsRecognized = isset($model->className) && class_exists($model->className);
        }

        if ($modelIsRecognized) {

            $className = $model->className;
            $view = $className::initWithModel($model);

        } else {

            $view = self::init();
            $view->model = $model;
        }

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
     * @note functional programming
     *
     * @return stdClass
     */
    public static function modelWithClassName($className) {
        $model              = new stdClass();
        $model->className   = (string)$className;

        return $model;
    }

    /**
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        if (isset($model->className) && $model->className != 'CBView') {
            $function = "{$model->className}::renderModelAsHTML";

            if (is_callable($function)) {
                call_user_func($function, $model);
            } else {
                $view = self::createViewWithModel($model);
                $view->renderHTML();
            }
        } else {
            if (CBSiteIsBeingDebugged) {
                $modelAsJSONAsHTML = ': ' . str_replace('--', ' - - ', json_encode($model));
            } else {
                $modelAsJSONAsHTML = '';
            }

            echo "<!-- CBView default output{$modelAsJSONAsHTML} -->";
        }
    }

    /**
     * @return void
     */
    public function renderHTML() {

        $className = get_class($this);

        echo "\n\n<!-- This is the default output for a `{$className}` class. -->\n\n";
    }

    /**
     * @return string
     */
    public static function modelToSearchText(stdClass $model = null) {
        if (isset($model->className) && $model->className != 'CBView') {
            $function = "{$model->className}::modelToSearchText";

            if (is_callable($function)) {
                return call_user_func($function, $model);
            }
        }

        return '';
    }

    /**
     * This function transforms a view specification into a model. This
     * function always succeeds. If the view class has no `specToModel`
     * function the model will be a copy of the specification. If the
     * specification has no `className` property, the `className` property
     * on the model will be set to "CBView".
     *
     * @note functional programming
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec = null) {
        if (isset($spec->className) && $spec->className != 'CBView') {
            $function = "{$spec->className}::specToModel";

            if (is_callable($function)) {
                return call_user_func($function, $spec);
            }

            // deprecated

            $function = "{$spec->className}::compileSpecificationModelToRenderModel";

            if (is_callable($function)) {
                error_log("`{$function}` should be renamed to `specToModel`");
                return call_user_func($function, $spec);
            }
        }

        if ($spec) {
            $model = json_decode(json_encode($spec));
        } else {
            $model = new stdClass();
        }

        $model->className = isset($model->className) ? $model->className : 'CBView';

        return $model;
    }
}

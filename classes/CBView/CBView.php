<?php

final class CBView {

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

        /**
         * 2015.03.31 TODO:
         *  When views were instances, we called the following generic model
         *  upgrader function. I'm not entirely sure that this function isn't
         *  still necessary.
         *
         *  It upgrades the model in place, so it's not a very good function.
         *
         *  CBViewModelUpgrader::upgradeModel($model);
         */

        if (isset($model->className) && $model->className != 'CBView') {
            $function = "{$model->className}::renderModelAsHTML";

            if (is_callable($function)) {
                return call_user_func($function, $model);
            }
        }

        if (Colby::siteIsBeingDebugged()) {
            $modelAsJSONAsHTML = ': ' . str_replace('--', ' - - ', json_encode($model));
        } else {
            $modelAsJSONAsHTML = '';
        }

        echo "<!-- CBView default output{$modelAsJSONAsHTML} -->";
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

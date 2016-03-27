<?php

final class CBView {

    /**
     * @return {array}
     */
    public static function modelToModelDependencies(stdClass $model) {
        if (is_callable($function = "{$model->className}::modelToModelDependencies")) {
            return call_user_func($function, $model);
        } else {
            return [];
        }
    }

    /**
     * @deprecated use (object)['className' => '<desired class name>']
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
                return call_user_func($function, $model);
            }
        }

        if (CBSitePreferences::debug()) {
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
     * This function makes it easy to create a view spec and render it from PHP
     * code. The code could easily call these lines itself or most of the time
     * even call the view's functions directly, but allowing rendering a spec
     * with a single function call saves many lines of code over an entire site.
     *
     * @param {stdClass} $spec
     *
     * return null
     */
    public static function renderSpecAsHTML(stdClass $spec) {
        $model = CBView::specToModel($spec);
        CBView::renderModelAsHTML($model);
    }

    /**
     * @deprecated use CBModel::specToOptionalModel
     *
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

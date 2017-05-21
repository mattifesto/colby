<?php

final class CBCustomView {

    /**
     * @param string? $model->customViewClassName
     *
     * @return string
     */
    static function modelToSearchText(stdClass $model) {
        if (!empty($model->customViewClassName) && is_callable($function = "{$model->customViewClassName}::modelToSearchText")) {
            return call_user_func($function , $model);
        } else {
            return '';
        }
    }

    /**
     * @param string? $model->customViewClassName
     *      The class name of the custom view to render. If this is empty or
     *      the class doesn't exist the view will not render.
     *
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        if (!empty($model->customViewClassName) && is_callable($function = "{$model->customViewClassName}::renderModelAsHTML")) {
            CBHTMLOutput::requireClassName($model->customViewClassName);
            call_user_func($function, $model);
        } else {
            $classNameAsHTMLComment = CBModel::value($model, 'customViewClassName', '<unset>', function ($value) {
                return '"' . $value . '"';
            });
            $classNameAsHTMLComment = preg_replace('/-->/', '-- >', $classNameAsHTMLComment);

            echo "<!-- CBCustomView unable to render for customViewClassName: {$classNameAsHTMLComment}. -->";
        }
    }

    /**
     * @param string? $spec->customViewClassName
     *      The class name of the custom view to render.
     * @param stdClass? $spec->properties
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'customViewClassName' => CBModel::value($spec, 'customViewClassName', '', 'trim'),
            'properties' => CBModel::value($spec, 'properties', (object)[]),
        ];
    }
}

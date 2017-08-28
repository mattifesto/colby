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
     * Either $model->customViewClassName or $model->properties->className must
     * be set to a valid view class name for this view to render anything.
     *
     * @param string? $model->customViewClassName
     *      The class name of the custom view to render. This does not need to
     *      be set if the `properties` object has a `className` property. This
     *      property is higher priority than the `className` property on the
     *      `properties` object.
     *
     * @param object? $model->properties
     *      This is the model to render. It was named properties before the
     *      behavior of this view was finalized.
     *
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        $modelToRender = CBModel::valueAsObject($model, 'properties');
        $customViewClassName = CBModel::value($model, 'customViewClassName', '', 'trim');

        if (!empty($customViewClassName)) {
            $modelToRender->className = $customViewClassName;
        }

        CBView::render($modelToRender);
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

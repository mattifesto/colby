<?php

final class CBCustomView {

    /**
     * @param string? $model->customViewClassName
     *
     * @return string|null
     */
    static function CBModel_toSearchText(stdClass $model) {
        $customModel = CBModel::valueToObject($model, 'properties');
        $customViewClassName = CBModel::value($model, 'customViewClassName', '', 'trim');

        if (!empty($customViewClassName)) {
            $customModel->className = $customViewClassName;
        }

        if (CBModel::value($customModel, 'className') !== __CLASS__) {
            return CBModel::toSearchText($customModel);
        } else {
            return null;
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
    static function CBView_render(stdClass $model) {
        $customModel = CBModel::valueToObject($model, 'properties');
        $customViewClassName = CBModel::value($model, 'customViewClassName', '', 'trim');

        if (!empty($customViewClassName)) {
            $customModel->className = $customViewClassName;
        }

        CBView::render($customModel);
    }

    /**
     * @param model $spec
     *
     *      {
     *          customViewClassName: ?string
     *
     *              The class name of the custom view to render.
     *
     *          properties: ?[model]
     *
     *              This should be named "customViewSpec", but it is not
     *              required to have its className property set.
     *      }
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'customViewClassName' => CBModel::value($spec, 'customViewClassName', '', 'trim'),
            'properties' => CBModel::valueToObject($spec, 'properties'),
        ];
    }
}

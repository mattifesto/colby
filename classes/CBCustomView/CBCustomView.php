<?php

final class CBCustomView {

    /* -- CBInstall interfaces -- -- -- -- -- */

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__
        );
    }
    /* CBInstall_install() */


    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */


    /* -- CBModel interfaces -- -- -- -- -- */

    /**
     * @param object $spec
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
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        return (object)[
            'customViewClassName' => trim(
                CBModel::valueToString($spec, 'customViewClassName')
            ),
            'properties' => CBModel::valueToObject($spec, 'properties'),
        ];
    }
    /* CBModel_build() */

    /**
     * @param string? $model->customViewClassName
     *
     * @return string|null
     */
    static function CBModel_toSearchText(stdClass $model): ?string {
        $customModel = CBModel::valueToObject($model, 'properties');

        $customViewClassName = trim(
            CBModel::valueToString($model, 'customViewClassName')
        );

        if (!empty($customViewClassName)) {
            $customModel->className = $customViewClassName;
        }

        if (CBModel::valueToString($customModel, 'className') !== __CLASS__) {
            return CBModel::toSearchText($customModel);
        } else {
            return null;
        }
    }
    /* CBModel_toSearchText */


    /* -- CBView interfaces -- -- -- -- -- */

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

        $customViewClassName = trim(
            CBModel::valueToString($model, 'customViewClassName')
        );

        if (!empty($customViewClassName)) {
            $customModel->className = $customViewClassName;
        }

        CBView::render($customModel);
    }
    /* CBView_render() */
}
/* CBCustomView */

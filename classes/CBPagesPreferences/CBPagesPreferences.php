<?php

/**
 * @deprecated 2019_08_17
 *
 *      In the past, this model was used to determine which views and layouts
 *      were available on the websites. Now views should be installed in the
 *      CBViewCatalog and layouts have been replaced by frames.
 *
 *      This class is currently still in use while code is being migrated to
 *      other classes.
 */
final class CBPagesPreferences {

    /**
     * @deprecated use CBPagesPreferences::modelID()
     */
    const ID = '3ff6fabd8a0da44f1b2d5f5faee6961af8e5a9df';

    const defaultClassNamesForLayouts = [
        'CBPageLayout',
    ];


    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(
            CBPagesPreferences::modelID()
        );

        if (!empty($spec)) {
            return;
        }

        CBDB::transaction(
            function () {
                CBModels::save(
                    (object)[
                        'className' => 'CBPagesPreferences',
                        'ID' => CBPagesPreferences::modelID(),
                    ]
                );
            }
        );
    }
    /* CBInstall_install() */


    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModels',
        ];
    }
    /* CBInstall_requiredClassNames() */


    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[];
        $model->deprecatedViewClassNames = [];
        $model->supportedViewClassNames = [];

        if (!empty($spec->supportedViewClassNames)) {
            $model->supportedViewClassNames =
            array_unique(
                preg_split(
                    '/[\s,]+/',
                    $spec->supportedViewClassNames,
                    null,
                    PREG_SPLIT_NO_EMPTY
                )
            );
        }

        if (!empty($spec->deprecatedViewClassNames)) {
            $model->deprecatedViewClassNames =
            array_unique(
                preg_split(
                    '/[\s,]+/',
                    $spec->deprecatedViewClassNames,
                    null,
                    PREG_SPLIT_NO_EMPTY
                )
            );
        }

        if (!empty($spec->classNamesForLayouts)) {
            $model->classNamesForLayouts =
            array_unique(
                preg_split(
                    '/[\s,]+/',
                    $spec->classNamesForLayouts,
                    null,
                    PREG_SPLIT_NO_EMPTY
                )
            );
        }

        return $model;
    }
    /* CBModel_build() */


    /**
     * @return [string]
     *
     *      An array of view class names that can be added to a page.
     */
    static function classNamesForAddableViews(): array {
        $supportedClassNames =
        CBPagesPreferences::classNamesForSupportedViews();

        $deprecatedClassNames =
        CBPagesPreferences::classNamesForDeprecatedViews();

        $classNames =
        array_unique(
            array_diff(
                $supportedClassNames,
                $deprecatedClassNames
            )
        );

        $classNames =
        array_filter(
            $classNames,
            function ($className) {
                return class_exists($className);
            }
        );

        return array_values($classNames);
    }
    /* classNamesForAddableViews() */


    /**
     * @return [string]
     *
     *      An array of site specific deprecated view class names.
     */
    static function classNamesForDeprecatedViews() {
        $model = CBModelCache::fetchModelByID(
            CBPagesPreferences::modelID()
        );

        $deprecatedViewClassNames =
        array_unique(
            array_merge(
                CBModel::valueToArray(
                    $model,
                    'deprecatedViewClassNames'
                ),
                CBViewCatalog::fetchDeprecatedViewClassNames()
            )
        );

        sort($deprecatedViewClassNames);

        return $deprecatedViewClassNames;
    }
    /* classNamesForDeprecatedViews() */


    /**
     * @return [string]
     *
     *      An array of view class names that can be edited for a page.
     */
    static function classNamesForEditableViews() {
        $editableViewClassNames =
        array_unique(
            array_merge(
                CBPagesPreferences::classNamesForSupportedViews(),
                CBPagesPreferences::classNamesForDeprecatedViews()
            )
        );

        $editableViewClassNames =
        array_values(
            array_filter(
                $editableViewClassNames,
                function ($className) {
                    return class_exists($className);
                }
            )
        );

        return $editableViewClassNames;
    }
    /* classNamesForEditableViews() */


    /**
     * Returns an array of class names for page layouts.
     *
     * @return [string]
     */
    static function classNamesForLayouts() {
        $model = CBModelCache::fetchModelByID(
            CBPagesPreferences::modelID()
        );

        $classNamesForLayouts = CBModel::valueToArray(
            $model,
            'classNamesForLayouts'
        );

        return array_unique(
            array_merge(
                CBPagesPreferences::defaultClassNamesForLayouts,
                $classNamesForLayouts
            )
        );
    }
    /* classNamesForLayouts() */


    /**
     * @return [string]
     *
     *      A alphabetized merge of the default supported view class names and
     *      the site specific supported view class names.
     */
    static function classNamesForSupportedViews(): array {
        $model = CBModelCache::fetchModelByID(
            CBPagesPreferences::modelID()
        );

        $supportedViewClassNames =
        array_unique(
            array_merge(
                CBModel::valueToArray(
                    $model,
                    'supportedViewClassNames'
                ),
                CBViewCatalog::fetchSupportedViewClassNames()
            )
        );

        sort($supportedViewClassNames);

        return $supportedViewClassNames;
    }
    /* classNamesForSupportedViews() */


    /**
     * @return string
     */
    static function modelID(): string {
        return '3ff6fabd8a0da44f1b2d5f5faee6961af8e5a9df';
    }
    /* modelID() */
}

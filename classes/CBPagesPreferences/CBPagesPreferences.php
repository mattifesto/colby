<?php

final class CBPagesPreferences {

    const ID = '3ff6fabd8a0da44f1b2d5f5faee6961af8e5a9df';
    const defaultClassNamesForLayouts = [
        'CBPageLayout',
    ];
    const defaultClassNamesForPageTemplates = [
        'CBStandardPageTemplate',
        'CBEmptyPageTemplate',
    ];
    const defaultClassNamesForSupportedViews = [
        'CBArtworkView',
        'CBContainerView',
        'CBCustomView',
        'CBLinkView1',
        'CBTextView2',
    ];

    /**
     * @return [string]
     *
     *      An array of view class names that can be added to a page.
     */
    static function classNamesForAddableViews() {
        $supportedClassNames = CBPagesPreferences::classNamesForSupportedViews();
        $deprecatedClassNames = CBPagesPreferences::classNamesForDeprecatedViews();
        $classNames = array_unique(array_diff($supportedClassNames, $deprecatedClassNames));
        $classNames = array_filter($classNames, function ($className) {
            return class_exists($className);
        });

        return array_values($classNames);
    }

    /**
     * @return [string]
     *
     *      An array of site specific deprecated view class names.
     */
    static function classNamesForDeprecatedViews() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);

        return $model->deprecatedViewClassNames;
    }

    /**
     * @return [string]
     *
     *      An array of view class names that can be edited for a page.
     */
    static function classNamesForEditableViews() {
        $supportedClassNames = CBPagesPreferences::classNamesForSupportedViews();
        $deprecatedClassNames = CBPagesPreferences::classNamesForDeprecatedViews();
        $classNames = array_unique(array_merge($supportedClassNames, $deprecatedClassNames));
        $classNames = array_filter($classNames, function ($className) {
            return class_exists($className);
        });

        return array_values($classNames);
    }

    /**
     * Returns an array of class names for page kinds.
     *
     * @return [string]
     */
    static function classNamesForKinds() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        return CBModel::value($model, 'classNamesForKinds', []);
    }

    /**
     * Returns an array of class names for page layouts.
     *
     * @return [string]
     */
    static function classNamesForLayouts() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        $classNamesForLayouts = CBModel::value($model, 'classNamesForLayouts', []);

        return array_unique(array_merge(CBPagesPreferences::defaultClassNamesForLayouts, $classNamesForLayouts));
    }

    /**
     * Returns an array of class names for page kinds.
     *
     * @return [string]
     */
    static function classNamesForSettings() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        return CBModel::value($model, 'classNamesForSettings', []);
    }

    /**
     * @return [string]
     *
     *      A alphabetized merge of the default supported view class names and
     *      the site specific supported view class names.
     */
    static function classNamesForSupportedViews() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        $classNames = array_merge(CBPagesPreferences::defaultClassNamesForSupportedViews, $model->supportedViewClassNames);
        $classNames = array_unique($classNames);
        sort($classNames);

        return $classNames;
    }

    /**
     * @return stdClass
     */
    static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Pages Preferences',
            'singularTitle' => 'Pages Preferences'
        ]);
    }

    /**
     * @return null
     */
    static function install() {
        $spec = CBModels::fetchSpecByID(CBPagesPreferences::ID);

        if ($spec === false) {
            $spec = CBModels::modelWithClassName(__CLASS__, [ 'ID' => CBPagesPreferences::ID ]);
        }

        CBModels::save([$spec]);
    }

    /**
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        $model = (object)['className' => __CLASS__];
        $model->deprecatedViewClassNames = [];
        $model->supportedViewClassNames = [];

        if (!empty($spec->supportedViewClassNames)) {
            $model->supportedViewClassNames = array_unique(preg_split(
                '/[\s,]+/', $spec->supportedViewClassNames, null, PREG_SPLIT_NO_EMPTY));
        }

        if (!empty($spec->deprecatedViewClassNames)) {
            $model->deprecatedViewClassNames = array_unique(preg_split(
                '/[\s,]+/', $spec->deprecatedViewClassNames, null, PREG_SPLIT_NO_EMPTY));
        }

        if (!empty($spec->classNamesForKinds)) {
            $model->classNamesForKinds = array_unique(preg_split(
                '/[\s,]+/', $spec->classNamesForKinds, null, PREG_SPLIT_NO_EMPTY));
        }

        if (!empty($spec->classNamesForLayouts)) {
            $model->classNamesForLayouts = array_unique(preg_split(
                '/[\s,]+/', $spec->classNamesForLayouts, null, PREG_SPLIT_NO_EMPTY));
        }

        if (!empty($spec->classNamesForSettings)) {
            $model->classNamesForSettings = array_unique(preg_split(
                '/[\s,]+/', $spec->classNamesForSettings, null, PREG_SPLIT_NO_EMPTY));
        }

        return $model;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}

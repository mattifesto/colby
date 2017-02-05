<?php

final class CBPagesPreferences {

    const ID = '3ff6fabd8a0da44f1b2d5f5faee6961af8e5a9df';

    /**
     * Returns an array of view class names that can be added to a page.
     *
     * @return [{string}]
     */
    public static function classNamesForAddableViews() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        $classNames = array_unique(array_diff($model->supportedViewClassNames, $model->deprecatedViewClassNames));
        $classNames = array_filter($classNames, function ($className) {
            return class_exists($className);
        });

        return array_values($classNames);
    }

    /**
     * Returns an array of view class names that can be edited for a page.
     *
     * @return [{string}]
     */
    public static function classNamesForEditableViews() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        $classNames = array_unique(array_merge($model->supportedViewClassNames, $model->deprecatedViewClassNames));
        $classNames = array_filter($classNames, function ($className) {
            return class_exists($className);
        });

        return array_values($classNames);
    }

    /**
     * Returns an array of class names for page kinds.
     *
     * @return [{string}]
     */
    public static function classNamesForKinds() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        return CBModel::value($model, 'classNamesForKinds', []);
    }

    /**
     * Returns an array of class names for page layouts.
     *
     * @return [{string}]
     */
    public static function classNamesForLayouts() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        return CBModel::value($model, 'classNamesForLayouts', []);
    }

    /**
     * Returns an array of class names for page kinds.
     *
     * @return [{string}]
     */
    public static function classNamesForSettings() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        return CBModel::value($model, 'classNamesForSettings', []);
    }

    /**
     * @return {stdClass}
     */
    public static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Pages Preferences',
            'singularTitle' => 'Pages Preferences'
        ]);
    }

    /**
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBPagesPreferences::ID);

        if ($spec === false) {
            $spec = CBModels::modelWithClassName(__CLASS__, [ 'ID' => CBPagesPreferences::ID ]);
            $spec->supportedViewClassNames = 'CBBackgroundView CBImageLinkView CBThemedTextView';
        }

        CBModels::save([$spec]);
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
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
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}

<?php

final class CBPagesPreferences {

    const ID = '3ff6fabd8a0da44f1b2d5f5faee6961af8e5a9df';
    const defaultClassNamesForLayouts = [
        'CBPageLayout',
    ];
    const defaultClassNamesForSupportedViews = [
        'CBArtworkView',
        'CBContainerView',
        'CBContainerView2',
        'CBCustomView',
        'CBLinkView1',
        'CBMenuView',
        'CBMessageView',
        'CBPageListView2',
        'CBPageTitleAndDescriptionView',
        'CBTextView2',
    ];

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBPagesPreferences::ID);

        if (empty($spec)) {
            CBDB::transaction(function () {
                CBModels::save((object)[
                    'className' => 'CBPagesPreferences',
                    'ID' => CBPagesPreferences::ID,
                ]);
            });
        }
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBModels'];
    }

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
     * 2018.03.15
     * @deprecated use CBPageSettings::defaultClassName()
     *
     *      Sites should implement CBPageSettings_defaultClassName::get()
     *
     * @return string
     */
    static function classNameForUnsetPageSettings() {
        if (is_callable($function = 'CBPageHelpers::classNameForUnsetPageSettings')) {
            return call_user_func($function);
        }

        $model = CBSitePreferences::model();

        if (empty($model->defaultClassNameForPageSettings)) {
            return 'CBPageSettingsForResponsivePages';
        } else {
            return $model->defaultClassNameForPageSettings; // deprecated
        }
    }

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

        return CBModel::valueToArray($model, 'deprecatedViewClassNames');
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
     * @deprecated use CBPagesPreferences::classNamesForPageKinds()
     */
    static function classNamesForKinds() {
        return CBPagesPreferences::classNamesForPageKinds();
    }

    /**
     * Returns an array of class names for page layouts.
     *
     * @return [string]
     */
    static function classNamesForLayouts() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        $classNamesForLayouts = CBModel::valueToArray($model, 'classNamesForLayouts');

        return array_unique(array_merge(CBPagesPreferences::defaultClassNamesForLayouts, $classNamesForLayouts));
    }

    /**
     * Returns an array of class names for page kinds. To customize the this
     * value implement CBPageHelpers::classNamesForKinds().
     *
     * @NOTE 2017.07.15 The `classNamesForKinds` property on the model has been
     *       deprecated and will be removed shortly.
     *
     * @return [string]
     */
    static function classNamesForPageKinds() {
        if (is_callable($function = 'CBPageHelpers::classNamesForPageKinds')) {
            return call_user_func($function);
        }

        // @deprecated
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        $kinds = CBModel::valueToArray($model, 'classNamesForKinds');

        if (empty($kinds)) {
            return CBPagesPreferences::classNamesForPageKindsDefault();
        } else {
            return $kinds;
        }
    }

    /**
     * The default value for CBPagesPreferences::classNamesForPageKinds()
     *
     * @return [string]
     */
    static function classNamesForPageKindsDefault() {
        return ['CBFrontPageKind'];
    }

    /**
     * Returns an array of class names that can be presented as options for page
     * editors for a page's page settings. Most of the time, a page editor
     * should not set a specific class name for pages settings.
     *
     * @return [string]
     */
    static function classNamesForSettings() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        return CBModel::valueToArray($model, 'classNamesForSettings');
    }

    /**
     * @return [string]
     *
     *      A alphabetized merge of the default supported view class names and
     *      the site specific supported view class names.
     */
    static function classNamesForSupportedViews() {
        $model = CBModelCache::fetchModelByID(CBPagesPreferences::ID);
        $supportedViewClassNames = CBModel::valueToArray($model, 'supportedViewClassNames');
        $classNames = array_merge(CBPagesPreferences::defaultClassNamesForSupportedViews, $supportedViewClassNames);
        $classNames = array_unique($classNames);
        sort($classNames);

        return $classNames;
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

<?php

final class CBPagesPreferences {

    const ID = '3ff6fabd8a0da44f1b2d5f5faee6961af8e5a9df';
    const defaultClassNamesForLayouts = [
        'CBPageLayout',
    ];
    /**
     * @deprecated use CBPagesPreferences::classNamesForPageTemplatesDefault()
     */
    const defaultClassNamesForPageTemplates = [
        'CBStandardPageTemplate',
        'CBEmptyPageTemplate',
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
     * This function is used by the `CBHTMLOutput` as the class name for page
     * settings when rendering a page whose model doesn't have a value set for
     * the `classNameForSettings` property. It is recommended that the property
     * not be set on a page model unless specific page settings are required.
     *
     * @NOTE 2017.07.09 This class now returns CBPageSettingsForResponsivePages if
     * no class name has been set in site preferences. If this causes problems
     * document them here, but I don't think it will. I can't currently think of
     * a time when having no page settings is a good thing.
     *
     * Furthermore, CBPageSettingsForResponsivePages should represent a usable
     * default for websites: simple, but usable visually and functionally.
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
        $classNamesForLayouts = CBModel::value($model, 'classNamesForLayouts', []);

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
        $kinds = CBModel::value($model, 'classNamesForKinds', []);

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
     * @return [string]
     */
    static function classNamesForPageTemplates() {
        if (is_callable($function = "CBPageHelpers::classNamesForPageTemplates")) {
            return call_user_func($function);
        } else {
            return CBPagesPreferences::classNamesForPageTemplatesDefault();
        }
    }

    /**
     * The default value for CBPagesPreferences::classNamesForPageTemplates()
     *
     * @return [string]
     */
    static function classNamesForPageTemplatesDefault() {
        return ['CBStandardPageTemplate', 'CBEmptyPageTemplate'];
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
    static function CBModel_toModel(stdClass $spec) {
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

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
     * @return model
     */
    static function CBModel_upgrade(stdClass $spec) {
        /**
         * 2018.04.11 Remove unused property
         * Can be removed after run on every site
         */
        if (isset($spec->defaultClassNameForPageSettings)) {
            unset($spec->defaultClassNameForPageSettings);

            CBLog::log((object)[
                'className' => __CLASS__,
                'severity' => 5,
                'message' => <<<EOT

                    Removed the "defaultClassNameForPageSettings" property from
                    the CBPagesPreferences spec because it is no longer used.

EOT
            ]);
        }

        /**
         * 2018.04.12 Remove unused property
         * Can be removed after run on every site
         */
        if (isset($spec->classNamesForSettings)) {
            unset($spec->classNamesForSettings);

            CBLog::log((object)[
                'className' => __CLASS__,
                'severity' => 5,
                'message' => <<<EOT

                    Removed the "classNamesForSettings" property from the
                    CBPagesPreferences spec because it is no longer used.

EOT
            ]);
        }

        /**
         * 2018.05.06 Remove unused property
         * Can be removed after run on every site
         */
        if (isset($spec->classNamesForKinds)) {
            unset($spec->classNamesForKinds);

            CBLog::log((object)[
                'className' => __CLASS__,
                'severity' => 5,
                'message' => <<<EOT

                    Removed the "classNamesForSettings" property from the
                    CBPagesPreferences spec because it is no longer used.

EOT
            ]);
        }

        return $spec;
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

        if (!empty($spec->classNamesForLayouts)) {
            $model->classNamesForLayouts = array_unique(preg_split(
                '/[\s,]+/', $spec->classNamesForLayouts, null, PREG_SPLIT_NO_EMPTY));
        }

        return $model;
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

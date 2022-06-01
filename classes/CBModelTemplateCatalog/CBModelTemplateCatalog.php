<?php

final class
CBModelTemplateCatalog
{
    /**
     * This variable will be set to a substitute ID to be used by
     * CBModelTemplateCatalog while tests are running.
     */
    static $testID = null;



    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        CBDB::transaction(
            function (
            ): void
            {
                CBModels::deleteByID(
                    CBModelTemplateCatalog::ID()
                );
            }
        );
    }
    // CBInstall_install()



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array
    {
        return
        [
            'CBModels',
        ];
    }
    // CBInstall_requiredClassNames()



    // -- CBModel interfaces



    /**
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $modelTemplateCatalogSpec
    ): stdClass
    {
        $modelTemplateCatalogModel =
        (object)
        [
            'livePageTemplateClassName' =>
            CBModel::valueToString(
                $modelTemplateCatalogSpec,
                'livePageTemplateClassName'
            ),
        ];

        CBModelTemplateCatalog::setTemplates(
            $modelTemplateCatalogModel,
            CBModelTemplateCatalog::getTemplates(
                $modelTemplateCatalogSpec
            )
        );

        return
        $modelTemplateCatalogModel;
    }
    /* CBModel_build() */



    // -- accessors



    /**
     * @param object $modelTemplateCatalogModel
     *
     * @return object
     *
     *      {
     *          <class name>: [<template class name>]
     *          ...
     *      }
     */
    static function
    getTemplates(
        stdClass $modelTemplateCatalogModel
    ): object
    {
        return
        CBModel::valueToObject(
            $modelTemplateCatalogModel,
            'templates'
        );
    }
    // getTemplates()



    /**
     * @param object $modelTemplateCatalogModel
     * @param object $newTemplates
     *
     *      {
     *          <class name>: [<template class name>]
     *          ...
     *      }
     *
     * @return void
     */
    static function
    setTemplates(
        stdClass $modelTemplateCatalogModel,
        stdClass $newTemplates
    ): void
    {
        $modelTemplateCatalogModel->templates =
        $newTemplates;
    }
    // setTemplates()



    /* -- functions -- */



    /**
     * @param ?object $updates
     *
     *      After the live page template spec is fetched, it will be merged with
     *      this object. The reason for this parameter is that after almost
     *      every call of this function, one of the next steps is to merge with
     *      another object to make property updates.
     *
     *      Passing the updates to this function results in cleaner and more
     *      concise code.
     *
     * @return object
     *
     *      Returns a the spec that has been installed as a starting point for a
     *      page on this site. The spec will be merged with the updates provided
     *      before being returned.
     */
    static function
    fetchLivePageTemplate(
        ?stdClass $updates = null
    ): stdClass {
        $model = CBModels::fetchModelByCBID(
            CBModelTemplateCatalog::ID()
        );

        $className = CBModel::valueToString(
            $model,
            'livePageTemplateClassName'
        );

        if (is_callable($function = "{$className}::CBModelTemplate_spec")) {
            $spec = call_user_func($function);

            unset($spec->sections);
        } else {
            $spec =  (object)[
                'className' => 'CBViewPage',
                'classNameForSettings' => 'CBPageSettingsForResponsivePages',
            ];
        }

        if ($updates !== null) {
            CBModel::merge($spec, $updates);
        }

        return $spec;
    }
    /* fetchLivePageTemplate() */



    /**
     * @return [string]
     */
    static function
    fetchTemplateClassNamesByTargetClassName(
        string $targetClassName
    ): array {
        $model = CBModels::fetchModelByCBID(
            CBModelTemplateCatalog::ID()
        );

        return CBModel::valueToArray(
            $model,
            "templates.{$targetClassName}"
        );
    }
    /* fetchTemplateClassNamesByTargetClassName() */



    /**
     * @return ID
     */
    static function ID(): string {
        return (
            CBModelTemplateCatalog::$testID ??
            'a50a379457147244325e3c512dadd5fac26daf11'
        );
    }



    /**
     * @param string $templateClassName
     *
     * @return void
     */
    static function install(string $templateClassName): void {
        $templateSpec = call_user_func(
            "{$templateClassName}::CBModelTemplate_spec"
        );

        $targetClassName = $templateSpec->className;

        $originalSpec = CBModels::fetchSpecByID(
            CBModelTemplateCatalog::ID()
        );

        if (empty($originalSpec)) {
            $originalSpec = (object)[
                'ID' => CBModelTemplateCatalog::ID(),
            ];
        }

        $spec = CBModel::clone($originalSpec);
        $spec->className = 'CBModelTemplateCatalog';

        $templates = CBModel::valueAsObject(
            $spec,
            'templates'
        );

        if (empty($templates)) {
            $templates = (object)[];
        }

        $templateClassNamesForTarget = CBModel::valueToArray(
            $templates,
            $targetClassName
        );

        array_push(
            $templateClassNamesForTarget,
            $templateClassName
        );

        $templates->{$targetClassName} =
        array_values(
            array_filter(
                array_unique(
                    $templateClassNamesForTarget
                )
            )
        );

        $spec->templates = $templates;

        if ($spec != $originalSpec) {
            CBDB::transaction(
                function () use ($spec) {
                    CBModels::save($spec);
                }
            );
        }
    }
    /* install() */


    /**
     * This template will be used by code that needs to create and render a page
     * live. This could be for a system notification, search results, or any
     * other page that is created and displayed live.
     *
     * @param string $templateClassName
     *
     *      The template should be a template for a CBViewPage model. The
     *      template can have views but they will be removed before the template
     *      is returned by the fetchLivePageTemplate(). This way, the standard
     *      page template for a site can often also be used as this template.
     *
     * @return void
     */
    static function installLivePageTemplate(string $templateClassName): void {
        $originalSpec = CBModels::fetchSpecByID(
            CBModelTemplateCatalog::ID()
        );

        if (empty($originalSpec)) {
            $originalSpec = (object)[
                'ID' => CBModelTemplateCatalog::ID(),
            ];
        }

        $spec = CBModel::clone($originalSpec);
        $spec->className = 'CBModelTemplateCatalog';
        $spec->livePageTemplateClassName = $templateClassName;

        if ($spec != $originalSpec) {
            CBDB::transaction(
                function () use ($spec) {
                    CBModels::save($spec);
                }
            );
        }
    }
    /* installLivePageTemplate() */
}

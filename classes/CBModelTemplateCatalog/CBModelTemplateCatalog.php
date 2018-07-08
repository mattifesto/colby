<?php

final class CBModelTemplateCatalog {

    /**
     * This variable will be set to a substitute ID to be used by
     * CBModelTemplateCatalog while tests are running.
     */
    static $testID = null;

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDB::transaction(function () {
            CBModels::deleteByID(CBModelTemplateCatalog::ID());
        });
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
        return (object)[
            'livePageTemplateClassName' => CBModel::valueToString($spec, 'livePageTemplateClassName'),
            'templates' => CBModel::valueToObject($spec, 'templates'),
        ];
    }

    /**
     * @return model
     *
     *      Returns a the spec that has been installed as a starting point for a
     *      page on this site.
     */
    static function fetchLivePageTemplate(): stdClass {
        $model = CBModels::fetchModelByID(CBModelTemplateCatalog::ID());

        $className = CBModel::valueToString($model, 'livePageTemplateClassName');

        if (is_callable($function = "{$className}::CBModelTemplate_spec")) {
            $spec = $function();

            unset($spec->sections);

            return $spec;
        }

        return (object)[
            'className' => 'CBViewPage',
        ];
    }

    /**
     * @return [string]
     */
    static function fetchTemplateClassNamesByTargetClassName(string $targetClassName): array {
        $model = CBModels::fetchModelByID(CBModelTemplateCatalog::ID());

        return CBModel::valueToArray($model, "templates.{$targetClassName}");
    }

    /**
     * @return ID
     */
    static function ID(): string {
        return CBModelTemplateCatalog::$testID ??
            'a50a379457147244325e3c512dadd5fac26daf11';
    }

    /**
     * @param string $templateClassName
     *
     * @return void
     */
    static function install(string $templateClassName): void {
        $templateSpec = call_user_func("{$templateClassName}::CBModelTemplate_spec");
        $targetClassName = $templateSpec->className;
        $originalSpec = CBModels::fetchSpecByID(CBModelTemplateCatalog::ID());

        if (empty($originalSpec)) {
            $originalSpec = (object)[
                'ID' => CBModelTemplateCatalog::ID(),
            ];
        }

        $spec = CBModel::clone($originalSpec);
        $spec->className = 'CBModelTemplateCatalog';
        $templates = CBModel::valueAsObject($spec, 'templates');

        if (empty($templates)) {
            $templates = (object)[];
        }

        $templateClassNamesForTarget = CBModel::valueToArray($templates, $targetClassName);

        array_push($templateClassNamesForTarget, $templateClassName);

        $templates->{$targetClassName} = array_values(array_filter(array_unique(
            $templateClassNamesForTarget
        )));

        $spec->templates = $templates;

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }

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
        $originalSpec = CBModels::fetchSpecByID(CBModelTemplateCatalog::ID());

        if (empty($originalSpec)) {
            $originalSpec = (object)[
                'ID' => CBModelTemplateCatalog::ID(),
            ];
        }

        $spec = CBModel::clone($originalSpec);
        $spec->className = 'CBModelTemplateCatalog';
        $spec->livePageTemplateClassName = $templateClassName;

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }
}

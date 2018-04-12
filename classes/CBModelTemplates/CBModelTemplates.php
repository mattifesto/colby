<?php

final class CBModelTemplates {

    /**
     * This variable will be set to a substitute ID to be used by
     * CBModelTemplates while tests are running.
     */
    static $testID = null;

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDB::transaction(function () {
            CBModels::deleteByID(CBModelTemplates::ID());
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
            'templates' => CBModel::valueToObject($spec, 'templates'),
        ];
    }

    /**
     * @return [string]
     */
    static function fetchTemplateClassNames(string $targetClassName): array {
        $model = CBModels::fetchModelByID(CBModelTemplates::ID());

        return CBModel::valueToArray($model, "templates.{$targetClassName}");
    }

    /**
     * @return ID
     */
    static function ID(): string {
        return CBModelTemplates::$testID ??
            'a50a379457147244325e3c512dadd5fac26daf11';
    }

    /**
     * @param string $templateClassName
     *
     * @return void
     */
    static function installTemplate(string $templateClassName): void {
        $templateSpec = call_user_func("{$templateClassName}::CBModelTemplate_spec");
        $targetClassName = $templateSpec->className;
        $originalSpec = CBModels::fetchSpecByID(CBModelTemplates::ID());

        if (empty($originalSpec)) {
            $originalSpec = (object)[
                'ID' => CBModelTemplates::ID(),
            ];
        }

        $spec = CBModel::clone($originalSpec);
        $spec->className = 'CBModelTemplates';

        if (empty($spec->templates)) {
            $spec->templates = (object)[];
        }

        if (empty($spec->templates->{$targetClassName})) {
            $templateClassNames = [$templateClassName];
        } else {
            $templateClassNames = $spec->templates->{$targetClassName};

            array_push($templateClassNames, $templateClassName);

            $templateClassNames = array_values(array_filter(array_unique(
                $templateClassNames
            )));
        }

        $spec->templates->{$targetClassName} = $templateClassNames;

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }
}

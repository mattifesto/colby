<?php

final class CBModelsAdminTemplateSelector {

    static $modelClassName = '';



    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return void
     */
    static function CBAdmin_initialize(): void {
        CBModelsAdminTemplateSelector::$modelClassName = cb_query_string_value(
            'modelClassName'
        );
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'models'
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = (
            CBModelsAdminTemplateSelector::$modelClassName .
            ' Template Selector'
        );
    }
    /* CBAdmin_render() */



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v589.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $templateClassNames = (
            CBModelTemplateCatalog::fetchTemplateClassNamesByTargetClassName(
                CBModelsAdminTemplateSelector::$modelClassName
            )
        );

        $templates = array_map(
            function ($templateClassName) {
                $functionName = "{$templateClassName}::CBModelTemplate_title";

                if (is_callable($functionName)) {
                    $title = call_user_func(
                        $functionName
                    );
                } else {
                    $title = $templateClassName;
                }

                return (object)[
                    'className' => $templateClassName,
                    'title' => $title,
                ];
            },
            $templateClassNames
        );

        return [
            [
                'CBModelsAdminTemplateSelector_modelClassName',
                CBModelsAdminTemplateSelector::$modelClassName,
            ],
            [
                'CBModelsAdminTemplateSelector_templates',
                $templates,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}

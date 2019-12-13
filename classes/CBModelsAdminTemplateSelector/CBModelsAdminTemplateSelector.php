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
        CBModelsAdminTemplateSelector::$modelClassName = cb_query_string_value('modelClassName');
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
        CBHTMLOutput::pageInformation()->title =
            CBModelsAdminTemplateSelector::$modelClassName .
            ' Template Selector';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUISectionItem4',
            'CBUIMessagePart',
            'CBUINavigationArrowPart',
            'CBUIStringsPart',
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $templateClassNames = CBModelTemplateCatalog::fetchTemplateClassNamesByTargetClassName(
            CBModelsAdminTemplateSelector::$modelClassName
        );

        $templates = array_map(function ($templateClassName) {
            if (is_callable($function = "{$templateClassName}::CBModelTemplate_title")) {
                $title = call_user_func($function);
            } else {
                $title = $templateClassName;
            }

            return (object)[
                'className' => $templateClassName,
                'title' => $title,
            ];
        }, $templateClassNames);

        return [
            ['CBModelsAdminTemplateSelector_modelClassName', CBModelsAdminTemplateSelector::$modelClassName],
            ['CBModelsAdminTemplateSelector_templates', $templates],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */

}

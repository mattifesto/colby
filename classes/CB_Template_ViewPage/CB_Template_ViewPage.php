<?php

final class CB_Template_ViewPage
{
    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_configure(): void
    {
        CBModelTemplateCatalog::install(
            'CB_Template_ViewPage'
        );
    }



    // -- CBModelTemplate interfaces



    /**
     * @return object
     */
    static function
    CBModelTemplate_spec(): stdClass
    {
        $viewPageSpec =
            CBModel::createSpec(
                'CBViewPage'
            );

        CBViewPage::setPageSettingsClassName(
            $viewPageSpec,
            'CB_StandardPageSettings'
        );

        return $viewPageSpec;
    }
    // CBModelTemplate_spec()



    /**
     * @return string
     */
    static function
    CBModelTemplate_title(): string
    {
        $title = 'View Page';

        return $title;
    }
    // CBModelTemplate_title()

}

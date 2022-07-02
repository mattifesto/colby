<?php

final class
CB_Template_Menu
{
    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_configure(
    ): void
    {
        CBModelTemplateCatalog::install(
            'CB_Template_Menu'
        );
    }



    // -- CBModelTemplate interfaces



    /**
     * @return object
     */
    static function
    CBModelTemplate_spec(
    ): stdClass
    {
        $menuSpec =
        CBModel::createSpec(
            'CBMenu'
        );

        return
        $menuSpec;
    }
    // CBModelTemplate_spec()



    /**
     * @return string
     */
    static function
    CBModelTemplate_title(
    ): string
    {
        return
        'Menu';
    }
    // CBModelTemplate_title()

}

<?php

final class
CB_Template_YouTubeChannel
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
            'CB_Template_YouTubeChannel'
        );
    }
    // CBInstall_configure()



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
            'CB_YouTubeChannel'
        );

        return $menuSpec;
    }
    // CBModelTemplate_spec()



    /**
     * @return string
     */
    static function
    CBModelTemplate_title(
    ): string
    {
        $title =
        'YouTube Channel';

        return $title;
    }
    // CBModelTemplate_title()

}

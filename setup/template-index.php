<?php

(function ()
{
    /**
     * 2023-08-09
     * Matt Calkins
     *
     *      We check for the existence of the autoload file because during the
     *      transition from Colby as a submodule to Colby as a PHP Composer
     *      library, it may not exist.
     */

    $phpComposerAutoloadFilepath =
    __DIR__ . '/vendor/autoload.php';

    if (
        file_exists($phpComposerAutoloadFilepath)
    ){
        require_once($phpComposerAutoloadFilepath);
    }

    /**
     * 2023-08-09
     * Matt Calkins
     *
     *      deprecated
     *
     *      This block is here to facilitate the transition from Colby as a
     *      submodule to Colby as a PHP Composer library. It should be removed
     *      in version 2024.
     */
    if (
        !class_exists('ColbyRequest')
    ) {
        require_once(__DIR__ . '/colby/init.php');
    }

    ColbyRequest::handleRequest();
}
)();

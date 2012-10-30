<?php

ColbyPage::requireVerifiedUser();

$stubs = ColbyRequest::decodedStubs();

if (count($stubs) == 2)
{
    // TODO: clean this up

    $absoluteHandlerFilename = COLBY_SITE_DIRECTORY . "/handlers/handle,admin~{$stubs[1]}.php";

    if (is_file($absoluteHandlerFilename))
    {
        goto done;
    }

    $absoluteHandlerFilename = COLBY_SITE_DIRECTORY . "/colby/handlers/handle,admin~{$stubs[1]}.php";

    if (is_file($absoluteHandlerFilename))
    {
        goto done;
    }

    // $absoluteHandlerFilename = 404 handler;

    done:

    include($absoluteHandlerFilename);
}

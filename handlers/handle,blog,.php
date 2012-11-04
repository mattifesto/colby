<?php

Colby::useBlog();

$stubs = ColbyRequest::decodedStubs();

if (count($stubs) > 2)
{
    return false;
}

$archive = ColbyBlog::archiveForStub($stubs[1]);

if (!$archive /* || !$archive->rootObject()->published */)
{
    return false;
}

include(COLBY_SITE_DIRECTORY . "/colby/handlers/handle,admin,blog,{$archive->rootObject()->type},display.php");

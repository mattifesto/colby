<?php

Colby::useBlog();

$stubs = ColbyRequest::decodedStubs();

if (count($stubs) > 2)
{
    return false;
}

$archive = ColbyBlog::archiveForStub($stubs[1]);

if (!$archive || !$archive->rootObject()->published)
{
    return false;
}

$blogViewFilename = "handle,admin,blog,{$archive->rootObject()->type},view.php";

include(Colby::findHandler($blogViewFilename));

<?php

$stub = implode('/', ColbyRequest::decodedStubs());

$archive = ColbyPage::archiveForStub($stub);

if (!$archive || !$archive->rootObject()->isPublished)
{
    return false;
}

$blogViewFilename = "handle,admin,blog,{$archive->rootObject()->modelId},view.php";

include(Colby::findHandler($blogViewFilename));

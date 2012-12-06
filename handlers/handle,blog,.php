<?php

$stub = implode('/', ColbyRequest::decodedStubs());

$archive = ColbyPage::archiveForStub($stub);

if (!$archive || !$archive->rootObject()->isPublished)
{
    return false;
}

$viewFilename = "handle,admin,view,{$archive->rootObject()->viewId}.php";

include(Colby::findHandler($viewFilename));

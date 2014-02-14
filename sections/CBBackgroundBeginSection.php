<?php

define('CBBackgroundBeginSectionTypeID', 'c4bacd7cf5315e5a07c20072cbb0f355bdb4b8bc');

$model                  = new stdClass();
$model->schema          = 'CBBackgroundBeginSection';
$model->schemaVersion   = 1;
$model->sectionID       = null;
$model->sectionTypeID   = CBBackgroundBeginSectionTypeID;

define('CBBackgroundBeginSectionModelJSON', json_encode($model));

if (isset($GLOBALS['CBSectionSnippets']))
{
    global $CBSectionSnippets;

    $CBSectionSnippets[CBBackgroundBeginSectionTypeID] = CBSystemDirectory .
        '/sections/CBBackgroundBeginSectionSnippet.php';
}

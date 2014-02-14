<?php

define('CBBackgroundEndSectionTypeID', '21a6820768477bf8909a64310753276d5805659b');

$model                  = new stdClass();
$model->schema          = 'CBBackgroundEndSection';
$model->schemaVersion   = 1;
$model->sectionID       = null;
$model->sectionTypeID   = CBBackgroundEndSectionTypeID;

define('CBBackgroundEndSectionModelJSON', json_encode($model));

if (isset($GLOBALS['CBSectionSnippets']))
{
    global $CBSectionSnippets;

    $CBSectionSnippets[CBBackgroundEndSectionTypeID] = CBSystemDirectory .
        '/sections/CBBackgroundEndSectionSnippet.php';
}

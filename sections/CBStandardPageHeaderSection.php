<?php

define('CBStandardPageHeaderSectionTypeID', 'a675a7197c61947684faa88711d746244a585669');

$model                  = new stdClass();
$model->schema          = 'CBStandardPageHeaderSection';
$model->schemaVersion   = 1;
$model->sectionID       = null;
$model->sectionTypeID   = CBStandardPageHeaderSectionTypeID;

define('CBStandardPageHeaderSectionModelJSON', json_encode($model));

if (isset($GLOBALS['CBSectionSnippets']))
{
    global $CBSectionSnippets;

    $CBSectionSnippets[CBStandardPageHeaderSectionTypeID] = CBSystemDirectory . '/sections/CBStandardPageHeaderSectionSnippet.php';
}

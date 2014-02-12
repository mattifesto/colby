<?php

define('CBStandardHeaderSectionTypeID', 'a675a7197c61947684faa88711d746244a585669');

$model                  = new stdClass();
$model->schema          = 'CBStandardHeaderSection';
$model->schemaVersion   = 1;
$model->sectionID       = null;
$model->sectionTypeID   = CBStandardHeaderSectionTypeID;

define('CBStandardHeaderSectionModelJSON', json_encode($model));

if (isset($GLOBALS['CBSectionSnippets']))
{
    global $CBSectionSnippets;

    $CBSectionSnippets[CBStandardHeaderSectionTypeID] = CBSystemDirectory . '/sections/CBStandardHeaderSectionSnippet.php';
}

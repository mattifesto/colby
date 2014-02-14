<?php

define('CBMarkaroundSectionTypeID', 'aa18decce62447c45a58252efd92c1a03dca9d65');

$model                      = new stdClass();
$model->schema              = 'CBMarkaroundSection';
$model->schemaVersion       = 1;
$model->sectionID           = null;
$model->sectionTypeID       = CBMarkaroundSectionTypeID;
$model->heading1            = '';
$model->heading1HTML        = '';
$model->heading2            = '';
$model->heading2HTML        = '';
$model->contentMarkaround   = '';
$model->contentHTML         = '';

define('CBMarkaroundSectionModelJSON', json_encode($model));

if (isset($GLOBALS['CBSectionSnippets']))
{
    global $CBSectionSnippets;

    $CBSectionSnippets[CBMarkaroundSectionTypeID] = CBSystemDirectory .
        '/sections/CBMarkaroundSectionSnippet.php';
}

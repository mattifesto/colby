<?php

define('CBStandardFooterSectionTypeID', 'fc9848b93965bdc1b03b650b744a9787ae7e7062');

$model                  = new stdClass();
$model->schema          = 'CBStandardFooterSection';
$model->schemaVersion   = 1;
$model->sectionID       = null;
$model->sectionTypeID   = CBStandardFooterSectionTypeID;

define('CBStandardFooterSectionModelJSON', json_encode($model));

if (isset($GLOBALS['CBSectionSnippets']))
{
    global $CBSectionSnippets;

    $CBSectionSnippets[CBStandardFooterSectionTypeID] = CBSystemDirectory . '/sections/CBStandardFooterSectionSnippet.php';
}

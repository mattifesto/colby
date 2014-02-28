<?php

define('CBStandardPageFooterSectionTypeID', 'fc9848b93965bdc1b03b650b744a9787ae7e7062');

$model                  = new stdClass();
$model->schema          = 'CBStandardPageFooterSection';
$model->schemaVersion   = 1;
$model->sectionID       = null;
$model->sectionTypeID   = CBStandardPageFooterSectionTypeID;

define('CBStandardPageFooterSectionModelJSON', json_encode($model));

global $CBSectionSnippets;

$CBSectionSnippets[CBStandardPageFooterSectionTypeID] = CBSystemDirectory . '/sections/CBStandardPageFooterSectionSnippet.php';

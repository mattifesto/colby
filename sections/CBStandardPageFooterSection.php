<?php

define('CBStandardPageFooterSectionTypeID', 'fc9848b93965bdc1b03b650b744a9787ae7e7062');

$model                  = new stdClass();
$model->schema          = 'CBStandardPageFooterSection';
$model->schemaVersion   = 1;
$model->sectionID       = null;
$model->sectionTypeID   = CBStandardPageFooterSectionTypeID;


$descriptor                         = new stdClass();
$descriptor->modelJSON              = json_encode($model);
$descriptor->name                   = 'CBStandardPageFooter';
$descriptor->snippetForHTML         = __DIR__ . '/CBStandardPageFooterSectionSnippet.php';
$descriptor->snippetForSearchText   = null;
$descriptor->URL                    = CBSystemURL . '/sections';
$descriptor->URLForEditorCSS        = null;
$descriptor->URLForEditorJavaScript = "{$descriptor->URL}/CBStandardPageFooterSectionEditor.js";


global $CBSections;
$CBSections[CBStandardPageFooterSectionTypeID] = $descriptor;

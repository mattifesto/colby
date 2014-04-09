<?php

define('CBStandardPageHeaderSectionTypeID', 'a675a7197c61947684faa88711d746244a585669');

$model                  = new stdClass();
$model->schema          = 'CBStandardPageHeaderSection';
$model->schemaVersion   = 1;
$model->sectionID       = null;
$model->sectionTypeID   = CBStandardPageHeaderSectionTypeID;


$descriptor                         = new stdClass();
$descriptor->modelJSON              = json_encode($model);
$descriptor->name                   = 'CBStandardPageHeader';
$descriptor->snippetForHTML         = __DIR__ . '/CBStandardPageHeaderSectionSnippet.php';
$descriptor->snippetForSearchText   = null;
$descriptor->URL                    = CBSystemURL . '/sections';
$descriptor->URLForEditorCSS        = null;
$descriptor->URLForEditorJavaScript = "{$descriptor->URL}/CBStandardPageHeaderSectionEditor.js";


global $CBSections;
$CBSections[CBStandardPageHeaderSectionTypeID] = $descriptor;

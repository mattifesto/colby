<?php

define('CBBackgroundSectionTypeID', 'c4bacd7cf5315e5a07c20072cbb0f355bdb4b8bc');

$model                                      = new stdClass();
$model->schema                              = 'CBBackgroundSection';
$model->schemaVersion                       = 1;
$model->sectionID                           = null;
$model->sectionTypeID                       = CBBackgroundSectionTypeID;
$model->backgroundColor                     = '';
$model->canHaveChildren                     = true;
$model->children                            = array();
$model->imageFilename                       = null;
$model->imageRepeatVertically               = false;
$model->imageRepeatHorizontally             = false;
$model->imageSizeX                          = null;
$model->imageSizeY                          = null;
$model->linkURL                             = '';
$model->linkURLHTML                         = '';
$model->minimumSectionHeightIsImageHeight   = true;


$descriptor                         = new stdClass();
$descriptor->modelJSON              = json_encode($model);
$descriptor->name                   = 'CBBackground';
$descriptor->snippetForHTML         = __DIR__ . '/CBBackgroundSectionSnippetForHTML.php';
$descriptor->snippetForSearchText   = null;
$descriptor->URL                    = CBSystemURL . '/sections';
$descriptor->URLForEditorCSS        = null;
$descriptor->URLForEditorJavaScript = "{$descriptor->URL}/CBBackgroundSectionEditor.js";


global $CBSections;
$CBSections[CBBackgroundSectionTypeID] = $descriptor;

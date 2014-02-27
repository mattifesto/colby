<?php

define('CBBackgroundBeginSectionTypeID', 'c4bacd7cf5315e5a07c20072cbb0f355bdb4b8bc');

$model                                      = new stdClass();
$model->schema                              = 'CBBackgroundBeginSection';
$model->schemaVersion                       = 1;
$model->sectionID                           = null;
$model->sectionTypeID                       = CBBackgroundBeginSectionTypeID;
$model->backgroundColor                     = '';
$model->canHaveChildren                     = true;
$model->children                            = array();
$model->imageFilename                       = null;
$model->imageRepeatVertically               = false;
$model->imageRepeatHorizontally             = false;
$model->imageSizeX                          = null;
$model->imageSizeY                          = null;
$model->minimumSectionHeightIsImageHeight   = true;

define('CBBackgroundBeginSectionModelJSON', json_encode($model));

global $CBSectionSnippets;

$CBSectionSnippets[CBBackgroundBeginSectionTypeID] = CBSystemDirectory .
    '/sections/CBBackgroundBeginSectionSnippet.php';

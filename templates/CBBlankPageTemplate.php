<?php

define('CBBlankPageTemplateID', '1f7abd3e66d2367c2efd2b94b9f4331e9578a75d');

$model                          = new stdClass();
$model->schema                  = 'CBPage';
$model->schemaVersion           = 1;
$model->rowID                   = null;
$model->dataStoreID             = null;
$model->groupID                 = null;
$model->title                   = '';
$model->titleHTML               = '';
$model->description             = '';
$model->descriptionHTML         = '';
$model->URI                     = null;
$model->URIIsStatic             = false;
$model->publicationTimeStamp    = null;
$model->isPublished             = false;
$model->publishedBy             = null;
$model->thumbnailURL            = null;
$model->sectionModels           = array();

define('CBBlankPageTemplateModelJSON', json_encode($model));

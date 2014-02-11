<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';
include_once CBSystemDirectory . '/classes/CBDataStore.php';


CBHTMLOutput::addCSSURL(CBSystemURL . '/css/equalize.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/html5shiv.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/ColbyEqualize.js');

if (defined('GOOGLE_UNIVERSAL_ANALYTICS_TRACKING_ID'))
{
    CBHTMLOutput::addJavaScriptSnippet(CBSystemDirectory . '/javascript/snippet-google-universal-analytics.php');
}

include_once Colby::findFile('page-renderer-configuration.php');


CBHTMLOutput::begin();


$dataStore  = new CBDataStore($dataStoreID);
$modelJSON  = file_get_contents($dataStore->directory() . '/model.json');
$model      = json_decode($modelJSON);

CBHTMLOutput::setTitleHTML($model->titleHTML);
CBHTMLOutput::setDescriptionHTML($model->descriptionHTML);

foreach ($model->sectionModels as $sectionModel)
{
    global $CBSectionSnippets;

    include $CBSectionSnippets[$sectionModel->sectionTypeID];
}


CBHTMLOutput::render();

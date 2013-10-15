<?php

/**
 * This file is a snippet for use with the `ColbyOutputManager` class. This
 * snippet will be included by an instance of that class and therefore `$this`
 * refers to the current instance of a `ColbyOutputManager`.
 */

$this->cssURLs[] = 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700';
$this->cssURLs[] = COLBY_SYSTEM_URL . '/css/equalize.css';
$this->cssURLs[] = COLBY_SYSTEM_URL . '/css/shared.css';

$this->javaScriptURLs[] = COLBY_SYSTEM_URL . '/javascript/html5shiv.js';
$this->javaScriptURLs[] = COLBY_SYSTEM_URL . '/javascript/ColbyEqualize.js';
$this->javaScriptURLs[] = COLBY_SYSTEM_URL . '/javascript/Colby.js';
$this->javaScriptURLs[] = COLBY_SYSTEM_URL . '/javascript/ColbySheet.js';

$this->javaScriptSnippetFilenames[] = COLBY_SYSTEM_DIRECTORY . '/javascript/snippet-google-universal-analytics.php';

<?php

/**
 * This file is a snippet for use with the `ColbyOutputManager` class. This
 * snippet will be included by an instance of that class and therefore `$this`
 * refers to the current instance of a `ColbyOutputManager`.
 */

$this->cssFilenames[] = 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700';
$this->cssFilenames[] = COLBY_SYSTEM_URL . '/css/equalize.css';
$this->cssFilenames[] = COLBY_SYSTEM_URL . '/css/shared.css';

$this->javaScriptFilenames[] = COLBY_SITE_URL . '/colby/javascript/html5shiv.js';
$this->javaScriptFilenames[] = COLBY_SITE_URL . '/colby/javascript/ColbyEqualize.js';
$this->javaScriptFilenames[] = COLBY_SITE_URL . '/colby/javascript/Colby.js';
$this->javaScriptFilenames[] = COLBY_SITE_URL . '/colby/javascript/ColbySheet.js';

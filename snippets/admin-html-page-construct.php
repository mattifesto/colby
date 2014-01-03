<?php

/**
 * This file is a snippet for use with the `ColbyOutputManager` class. This
 * snippet will be included by an instance of that class and therefore `$this`
 * refers to the current instance of a `ColbyOutputManager`.
 */


/**
 * CSS URLs
 */

$this->cssURLs[] = 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700';
$this->cssURLs[] = 'https://fonts.googleapis.com/css?family=Source+Code+Pro';
$this->cssURLs[] = COLBY_SYSTEM_URL . '/css/equalize.css';
$this->cssURLs[] = COLBY_SYSTEM_URL . '/css/shared.css';
$this->cssURLs[] = COLBY_SYSTEM_URL . '/css/standard.css';
$this->cssURLs[] = COLBY_SYSTEM_URL . '/css/standard-formatted-content.css';
$this->cssURLs[] = COLBY_SYSTEM_URL . '/css/admin.css';

if (ColbyRequest::$archive)
{
    $documentGroupId = ColbyRequest::$archive->valueForKey('documentGroupId');
    $documentTypeId = ColbyRequest::$archive->valueForKey('documentTypeId');

    $documentTypeStyleSheetURL = Colby::findFileForDocumentType('view.css',
                                                                $documentGroupId,
                                                                $documentTypeId,
                                                                Colby::returnURL);

    if ($documentTypeStyleSheetURL)
    {
        $this->cssURLs[] = $documentTypeStyleSheetURL;
    }
}

/**
 * Javascript URLs
 */

$this->javaScriptURLs[] = COLBY_SYSTEM_URL . '/javascript/html5shiv.js';
$this->javaScriptURLs[] = COLBY_SYSTEM_URL . '/javascript/ColbyEqualize.js';
$this->javaScriptURLs[] = COLBY_SYSTEM_URL . '/javascript/Colby.js';
$this->javaScriptURLs[] = COLBY_SYSTEM_URL . '/javascript/ColbyFormManager.js';
$this->javaScriptURLs[] = COLBY_SYSTEM_URL . '/javascript/ColbySheet.js';

/**
 * Javascript snippets
 */

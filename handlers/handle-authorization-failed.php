<?php

$page = new ColbyOutputManager();

$page->titleHTML = 'Authorization Failed';
$page->descriptionHTML = 'You are not authorized to view this page.';

$page->begin();

include Colby::findSnippet('authenticate.php');

$page->end();

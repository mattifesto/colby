<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Authorization Failed');
CBHTMLOutput::setDescriptionHTML('You are not authorized to view this page.');

include Colby::findSnippet('authenticate.php');

CBHTMLOutput::render();

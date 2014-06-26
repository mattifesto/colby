<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Authorization Failed');
CBHTMLOutput::setDescriptionHTML('You are not authorized to view this page.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

include Colby::findSnippet('authenticate.php');

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

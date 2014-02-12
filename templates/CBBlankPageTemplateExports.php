<?php

include_once CBSystemDirectory . '/templates/CBBlankPageTemplate.php';


$descriptor               = new stdClass();
$descriptor->name         = 'Blank';
$descriptor->modelJSON    = CBBlankPageTemplateModelJSON;

CBHTMLOutput::exportListItem('CBPageTemplateDescriptors', CBBlankPageTemplateID, $descriptor);

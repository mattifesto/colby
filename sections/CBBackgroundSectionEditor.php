<?php

include_once CBSystemDirectory . '/sections/CBBackgroundSection.php';


CBHTMLOutput::exportConstant('CBBackgroundSectionTypeID');

$descriptor                 = new stdClass();
$descriptor->name           = "Background";
$descriptor->modelJSON      = CBBackgroundSectionModelJSON;

CBHTMLOutput::exportListItem('CBSectionDescriptors', CBBackgroundSectionTypeID, $descriptor);

CBHTMLOutput::addCSSURL(       CBSystemURL . '/sections/CBBackgroundSectionEditor.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/sections/CBBackgroundSectionEditor.js');

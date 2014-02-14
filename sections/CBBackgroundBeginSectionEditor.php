<?php

include_once CBSystemDirectory . '/sections/CBBackgroundBeginSection.php';


CBHTMLOutput::exportConstant('CBBackgroundBeginSectionTypeID');

$descriptor                 = new stdClass();
$descriptor->name           = "Background Begin";
$descriptor->modelJSON      = CBBackgroundBeginSectionModelJSON;

CBHTMLOutput::exportListItem('CBSectionDescriptors', CBBackgroundBeginSectionTypeID, $descriptor);

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/sections/CBBackgroundBeginSectionEditor.js');

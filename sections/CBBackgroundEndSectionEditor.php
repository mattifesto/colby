<?php

include_once CBSystemDirectory . '/sections/CBBackgroundEndSection.php';


CBHTMLOutput::exportConstant('CBBackgroundEndSectionTypeID');

$descriptor                 = new stdClass();
$descriptor->name           = "Background End";
$descriptor->modelJSON      = CBBackgroundEndSectionModelJSON;

CBHTMLOutput::exportListItem('CBSectionDescriptors', CBBackgroundEndSectionTypeID, $descriptor);

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/sections/CBBackgroundEndSectionEditor.js');

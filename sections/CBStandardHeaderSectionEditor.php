<?php

include_once CBSystemDirectory . '/sections/CBStandardHeaderSection.php';


CBHTMLOutput::exportConstant('CBStandardHeaderSectionTypeID');

$descriptor                 = new stdClass();
$descriptor->name           = "Standard Header";
$descriptor->modelJSON      = CBStandardHeaderSectionModelJSON;

CBHTMLOutput::exportListItem('CBSectionDescriptors', CBStandardHeaderSectionTypeID, $descriptor);

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/sections/CBStandardHeaderSectionEditor.js');

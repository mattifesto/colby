<?php

include_once CBSystemDirectory . '/sections/CBMarkaroundSection.php';


CBHTMLOutput::exportConstant('CBMarkaroundSectionTypeID');

$descriptor                 = new stdClass();
$descriptor->name           = "Markaround";
$descriptor->modelJSON      = CBMarkaroundSectionModelJSON;

CBHTMLOutput::exportListItem('CBSectionDescriptors', CBMarkaroundSectionTypeID, $descriptor);

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/sections/CBMarkaroundSectionEditor.js');

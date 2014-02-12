<?php

include_once CBSystemDirectory . '/sections/CBStandardFooterSection.php';


CBHTMLOutput::exportConstant('CBStandardFooterSectionTypeID');

$descriptor                 = new stdClass();
$descriptor->name           = "Standard Footer";
$descriptor->modelJSON      = CBStandardFooterSectionModelJSON;

CBHTMLOutput::exportListItem('CBSectionDescriptors', CBStandardFooterSectionTypeID, $descriptor);

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/sections/CBStandardFooterSectionEditor.js');

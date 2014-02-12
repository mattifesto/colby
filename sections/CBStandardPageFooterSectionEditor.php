<?php

include_once CBSystemDirectory . '/sections/CBStandardPageFooterSection.php';


CBHTMLOutput::exportConstant('CBStandardPageFooterSectionTypeID');

$descriptor                 = new stdClass();
$descriptor->name           = "Standard Page Footer";
$descriptor->modelJSON      = CBStandardPageFooterSectionModelJSON;

CBHTMLOutput::exportListItem('CBSectionDescriptors', CBStandardPageFooterSectionTypeID, $descriptor);

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/sections/CBStandardPageFooterSectionEditor.js');

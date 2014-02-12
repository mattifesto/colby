<?php

include_once CBSystemDirectory . '/sections/CBStandardPageHeaderSection.php';


CBHTMLOutput::exportConstant('CBStandardPageHeaderSectionTypeID');

$descriptor                 = new stdClass();
$descriptor->name           = "Standard Page Header";
$descriptor->modelJSON      = CBStandardPageHeaderSectionModelJSON;

CBHTMLOutput::exportListItem('CBSectionDescriptors', CBStandardPageHeaderSectionTypeID, $descriptor);

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/sections/CBStandardPageHeaderSectionEditor.js');

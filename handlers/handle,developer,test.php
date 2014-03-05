<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Colby Unit Tests');
CBHTMLOutput::setDescriptionHTML('Developer tests to make sure there are no regressions in functionality.');

include CBSystemDirectory . '/sections/equalize.php';

CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,developer,test.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Tests.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Colby.js');


$selectedMenuItemID     = 'test';
$selectedSubmenuItemID  = 'test';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>
</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

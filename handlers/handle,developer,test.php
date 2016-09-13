<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Colby Unit Tests');
CBHTMLOutput::setDescriptionHTML('Developer tests to make sure there are no regressions in functionality.');

CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,developer,test.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,developer,test.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Tests.js');

CBAdminPageMenuView::renderModelAsHTML((object)[
    'selectedMenuItemName' => 'test',
    'selectedSubmenuItemName' => 'test',
]);

?>

<main>
</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Menus');
CBHTMLOutput::setDescriptionHTML('Create and edit menus.');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,develop,menus.js');

CBAdminPageMenuView::renderModelAsHTML((object)[
    'selectedMenuItemName' => 'develop',
    'selectedSubmenuItemName' => 'menus',
]);

?>

<main>
</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Menus');
CBHTMLOutput::setDescriptionHTML('Create and edit menus.');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,develop,menus.js');

$selectedMenuItemID     = 'develop';
$selectedSubmenuItemID  = 'menus';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>
</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

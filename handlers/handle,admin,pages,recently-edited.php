<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Recently Edited Pages');
CBHTMLOutput::setDescriptionHTML('A list of the most recently edited pages.');

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'recently-edited';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main class="CBSystemFont">

    <?php CBRecentlyEditedPagesView::renderModelAsHTML(); ?>

</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();

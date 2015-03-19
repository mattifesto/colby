<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Recently Edited Pages');
CBHTMLOutput::setDescriptionHTML('A list of the most recently edited pages.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

$selectedMenuItemID     = 'pages';
$selectedSubmenuItemID  = 'recently-edited';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>

    <?php CBRecentlyEditedPagesView::renderModelAsHTML(); ?>

</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();

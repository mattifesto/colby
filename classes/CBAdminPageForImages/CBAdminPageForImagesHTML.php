<?php

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('develop');
$menu->setSelectedSubmenuItemName('images');
$menu->renderHTML();

?>

<main>

    <?php

    $head = CPView::specForClassName('CPAdminSectionHeaderView');
    $head->title = 'Images Administration';

    CPView::renderAsHTML(CPView::compile($head));

    ?>

</main>

<?php

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

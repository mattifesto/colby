<?php

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'develop';
$spec->selectedSubmenuItemName  = 'images';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

?>

<main>

    <?php

    $head = CPView::specForClassName('CPAdminSectionHeaderView');
    $head->title = 'Images Administration';

    CPView::renderAsHTML(CPView::compile($head));

    $list = CPView::specForClassName('CPAdminImageListView');

    CPView::renderASHTML(CPView::compile($list));

    ?>

</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

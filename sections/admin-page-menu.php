<?php

/**
 * Use of this file is deprecated. The caller should replace the include of
 * this file with direct use of the CBAdminPageMenuView.
 */

if (!isset($selectedMenuItemID))
{
    $selectedMenuItemID = null;
}

if (!isset($selectedSubmenuItemID))
{
    $selectedSubmenuItemID = null;
}

$spec                           = new stdClass();
$spec->selectedMenuItemName     = $selectedMenuItemID;
$spec->selectedSubmenuItemName  = $selectedSubmenuItemID;

CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

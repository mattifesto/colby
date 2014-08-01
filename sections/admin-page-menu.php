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

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName($selectedMenuItemID);
$menu->setSelectedSubmenuItemName($selectedSubmenuItemID);
$menu->renderHTML();

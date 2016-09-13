<?php

/**
 * Use of this file is deprecated. The caller should replace the include of
 * this file with direct use of the CBAdminPageMenuView.
 */

call_user_func(function () {
    $URI = $_SERVER['REQUEST_URI'];
    $file = __FILE__;
    CBLog::addMessage('Deprecated', 4, "A request for the URI '{$URI}' loaded the deprecated menu file '{$file}'.");
});

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

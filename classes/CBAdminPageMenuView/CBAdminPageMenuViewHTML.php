<section class="CBAdminPageMenuView">

    <?php

    $selectedMenuItemName = CBModel::value($model, 'selectedMenuItemName');

    self::renderMenu($menuModel, $selectedMenuItemName, 'CBMenu');

    if (!empty($selectedMenuItemName) &&
        isset($menuModel->{$selectedMenuItemName}->submenu))
    {
        $submenu = $menuModel->{$selectedMenuItemName}->submenu;
        $selectedSubmenuItemName = CBModel::value($model, 'selectedSubmenuItemName');

        self::renderMenu($submenu, $selectedSubmenuItemName, 'CBSubmenu');
    }

    ?>

</section>

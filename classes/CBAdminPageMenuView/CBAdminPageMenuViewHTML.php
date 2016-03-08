<section class="CBAdminPageMenuView">

    <?php

    $selectedMenuItemName       = $model->selectedMenuItemName;

    self::renderMenu($menuModel, $selectedMenuItemName, 'CBMenu');

    if ($selectedMenuItemName &&
        isset($menuModel->{$selectedMenuItemName}->submenu))
    {
        $submenu                    = $menuModel->{$selectedMenuItemName}->submenu;
        $selectedSubmenuItemName    = $model->selectedSubmenuItemName;

        self::renderMenu($submenu, $selectedSubmenuItemName, 'CBSubmenu');
    }

    ?>

</section>

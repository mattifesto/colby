<?php

CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
CBHTMLOutput::addCSSURL(CBSystemURL . '/classes/CBAdminPageMenuView/CBAdminPageMenuViewHTML.css');

?>

<section class="CBAdminPageMenuView">

    <?php

    $selectedMenuItemName       = $this->model->selectedMenuItemName;

    $this->renderMenu($this->menuModel, $selectedMenuItemName, 'CBMenu');

    if ($selectedMenuItemName &&
        isset($this->menuModel->{$selectedMenuItemName}->submenu))
    {
        $submenu                    = $this->menuModel->{$selectedMenuItemName}->submenu;
        $selectedSubmenuItemName    = $this->model->selectedSubmenuItemName;

        $this->renderMenu($submenu, $selectedSubmenuItemName, 'CBSubmenu');
    }

    ?>

</section>

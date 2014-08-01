<?php

CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
CBHTMLOutput::addCSSURL(CBSystemURL . '/classes/CBAdminPageMenuView/CBAdminPageMenuViewHTML.css');

?>

<section class="CBAdminPageMenuView">

    <?php

    $this->renderMenu($this->menuModel, $this->selectedMenuItemName, 'CBMenu');

    if ($this->selectedMenuItemName &&
        $this->menuModel->{$this->selectedMenuItemName}->submenu)
    {
        $submenu = $this->menuModel->{$this->selectedMenuItemName}->submenu;

        $this->renderMenu($submenu, $this->selectedSubmenuItemName, 'CBSubmenu');
    }

    ?>

</section>

<?php

include_once Colby::findFile('snippets/menu-items-admin.php');


CBHTMLOutput::addCSSURL(CBSystemURL . '/sections/admin-page-menu.css');

global $CBAdminMenu;

if (!isset($selectedMenuItemID))
{
    $selectedMenuItemID = null;
}

CBRenderMenu($CBAdminMenu, $selectedMenuItemID, 'CBMainMenu');

if ($selectedMenuItemID &&
    $CBAdminMenu->{$selectedMenuItemID}->submenu)
{
    if (!isset($selectedSubmenuItemID))
    {
        $selectedSubmenuItemID = null;
    }

    CBRenderMenu($CBAdminMenu->{$selectedMenuItemID}->submenu, $selectedSubmenuItemID, 'CBSubMenu');
}

/**
 * @return void
 */
function CBRenderMenu($menu, $selectedItemID, $class)
{
    ?>

    <nav class="CBMenu <?php echo $class; ?>">
        <ul><!--

            <?php

            foreach ($menu as $itemID => $item)
            {
                $classAttribute = '';

                if ($selectedItemID == $itemID)
                {
                    $classAttribute = ' class="CBSelected"';
                }

                echo <<<EOT

                --><li{$classAttribute}>
                    <a href="{$item->URI}">{$item->nameHTML}</a>
                </li><!--

EOT;
            }

            ?>

        --></ul>
    </nav>

    <?php
}

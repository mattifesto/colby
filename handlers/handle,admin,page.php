<?php

/**
 * Required class methods:
 *  adminPagePermissions()
 *
 * Recommended class methods:
 *  adminPageMenuNamePath()
 *
 * Optional class methods:
 *  adminPageRenderContent()
 *
 * The class is automatically added to the list of required classes which also
 * makes the functions used by required classes optional:
 *  requiredClassNames()
 *  requiredCSSURLs()
 *  requiredJavaScriptURLs()
 */
$class = isset($_GET['class']) ? $_GET['class'] : null;

if (is_callable($getPermissions = "{$class}::adminPagePermissions")) {
    $permissions = call_user_func($getPermissions);

    if ('Public' === $permissions->group || ColbyUser::current()->isOneOfThe($permissions->group)) {
        CBHTMLOutput::begin();
        CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
        CBHTMLOutput::requireClassName($class);

        $menuSpec = new stdClass();

        if (is_callable($function = "{$class}::adminPageMenuNamePath")) {
            $names = call_user_func($function);
            if (!empty($names[0])) { $menuSpec->selectedMenuItemName = $names[0]; }
            if (!empty($names[1])) { $menuSpec->selectedSubmenuItemName = $names[1]; }
        }

        CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($menuSpec));

        ?><main class="CBUIRoot <?= $class ?>" style="flex: 1 0 auto;"><?php
        if (is_callable($function = "{$class}::adminPageRenderContent")) {
            call_user_func($function);
        }
        ?></main><?php
        CBAdminPageFooterView::renderModelAsHTML();
        CBHTMLOutput::render();
    } else {
        return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
    }
} else {
    return 0; /* !1 -> 404 */
}

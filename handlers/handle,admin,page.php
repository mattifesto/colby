<?php

/**
 * Required class methods:
 *  adminPagePermissions()
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
        CBAdminPageMenuView::renderModelAsHTML();
        ?><main class="CBUIRoot"><?php
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

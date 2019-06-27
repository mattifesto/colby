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
 *  CBHTMLOutput_requiredClassNames()
 *  CBHTMLOutput_CSSURLs()
 *  CBHTMLOutput_JavaScriptURLs()
 */
$class = isset($_GET['class']) ? $_GET['class'] : null;

if (is_callable($getPermissions = "{$class}::adminPagePermissions")) {
    $permissions = call_user_func($getPermissions);

    if ('Public' === $permissions->group || ColbyUser::current()->isOneOfThe($permissions->group)) {
        CBHTMLOutput::begin();
        CBHTMLOutput::pageInformation()->classNameForPageSettings = 'CBPageSettingsForAdminPages';
        CBHTMLOutput::requireClassName('CBUI');
        CBHTMLOutput::requireClassName($class);

        $menuModel = (object)[
            'className' => 'CBAdminPageMenuView',
        ];

        if (is_callable($function = "{$class}::adminPageMenuNamePath")) {
            $names = call_user_func($function);
            CBHTMLOutput::pageInformation()->selectedMenuItemNames = $names;
        }

        CBView::render($menuModel);

        ?><main class="CBUIRoot <?= $class ?>"><?php
        if (is_callable($function = "{$class}::adminPageRenderContent")) {
            call_user_func($function);
        }
        ?></main><?php
        CBView::render((object)[
            'className' => 'CBAdminPageFooterView',
        ]);
        CBHTMLOutput::render();
    } else {
        return include cbsysdir() . '/handlers/handle-authorization-failed.php';
    }
} else {
    return 0; /* !1 -> 404 */
}

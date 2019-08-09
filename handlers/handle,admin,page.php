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

$adminClassName = cb_query_string_value('class');
$getPermissionsFunctionName = "{$adminClassName}::adminPagePermissions";

if (!is_callable($getPermissionsFunctionName)) {
    $URL = cbsiteurl() . "/admin/?c={$adminClassName}";

    header("Location: {$URL}");

    return 1;
}

$permissions = call_user_func($getPermissionsFunctionName);

if (
    'Public' === $permissions->group ||
    ColbyUser::current()->isOneOfThe($permissions->group)
) {
    CBHTMLOutput::begin();

    try {
        CBHTMLOutput::pageInformation()->classNameForPageSettings =
        'CBPageSettingsForAdminPages';

        CBHTMLOutput::requireClassName('CBUI');
        CBHTMLOutput::requireClassName($adminClassName);

        $menuModel = (object)[
            'className' => 'CBAdminPageMenuView',
        ];

        if (is_callable($function = "{$adminClassName}::adminPageMenuNamePath")) {
            $names = call_user_func($function);
            CBHTMLOutput::pageInformation()->selectedMenuItemNames = $names;
        }

        CBView::render($menuModel);

        ?>

        <main class="CBUIRoot <?= $adminClassName ?>">

            <?php

            $renderFunctionName = "{$adminClassName}::adminPageRenderContent";

            if (is_callable($renderFunctionName)) {
                call_user_func($renderFunctionName);
            }

            ?>

        </main>

        <?php

        CBView::render(
            (object)[
                'className' => 'CBAdminPageFooterView',
            ]
        );

        CBHTMLOutput::render();
    } catch(Throwable $throwable) {
        CBHTMLOutput::reset();
        CBErrorHandler::report($throwable);
        CBErrorHandler::renderErrorReportPage($throwable);
    }
} else {
    return include cbsysdir() . '/handlers/handle-authorization-failed.php';
}

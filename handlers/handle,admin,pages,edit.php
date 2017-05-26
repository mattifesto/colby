<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

if (!isset($_GET['data-store-id'])) {
    if (isset($_GET['id-to-copy'])) {
        $IDToCopy = $_GET['id-to-copy'];
        $IDToCopy = "&id-to-copy={$IDToCopy}";
    } else {
        $IDToCopy = '';
    }

    $ID = CBHex160::random();

    header("Location: /admin/pages/edit/?data-store-id={$ID}{$IDToCopy}");

    exit;
}


CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Page Editor');
CBHTMLOutput::setDescriptionHTML('This is an app for editing pages.');
CBHTMLOutput::requireClassName('CBDefaultEditor');
CBHTMLOutput::requireClassName('CBViewPageEditor');

/**
 * Include all of the supported views.
 */

$classNamesForEditableModels = array_merge(
    CBPagesPreferences::classNamesForEditableViews(),
    CBPagesPreferences::classNamesForLayouts()
);

foreach ($classNamesForEditableModels as $className) {
    if (class_exists($editorClassName = "{$className}Editor")) {
        CBHTMLOutput::requireClassName($editorClassName);
    }
}

/**
 * Create the list of selectable views available to be added to the page.
 */

CBHTMLOutput::exportVariable('CBPageEditorAvailableViewClassNames', CBPagesPreferences::classNamesForAddableViews());

/**
 * Export page templates
 */

global $CBPageEditorAvailablePageTemplateClassNames;

if ($CBPageEditorAvailablePageTemplateClassNames || class_exists('CBPageTemplateList') || class_exists('CBViewPageTemplates')) {
    throw new Exception('This website needs to be updated to use the CBPageHelpers::availableTemplateClassNames().');
} else {
    if (is_callable($function = "CBPageHelpers::classNamesForPageTemplates")) {
        $classNames = call_user_func($function);
    } else {
        $classNames = CBPagesPreferences::defaultClassNamesForPageTemplates;
    }

    foreach ($classNames as $pageTemplateClassName) {
        if (is_callable($function = "{$pageTemplateClassName}::model")) {
            $modelJSON = json_encode(call_user_func($function));

            if (is_callable($function = "{$pageTemplateClassName}::title")) {
                $title = call_user_func($function);
            } else {
                $title = 'Unnamed Template';
            }

            $descriptor = (object)[
                'modelJSON' => $modelJSON,
                'title' => $title,
            ];

            CBHTMLOutput::exportListItem('CBPageTemplateDescriptors', $pageTemplateClassName, $descriptor);
        }
    }
}

/**
 * Export query variables
 *
 * 2014.04.08
 *  This can be removed when this page moves to storing the archive ID as a hash
 *  variable.
 */

CBHTMLOutput::exportVariable('CBURLQueryVariables', $_GET);

CBAdminPageMenuView::renderModelAsHTML((object)[
    'selectedMenuItemName' => 'pages',
]);

?>

<main class="CBUIRoot">
</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

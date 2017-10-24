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
 * Export page templates
 */

global $CBPageEditorAvailablePageTemplateClassNames;

if ($CBPageEditorAvailablePageTemplateClassNames || class_exists('CBPageTemplateList') || class_exists('CBViewPageTemplates')) {
    throw new Exception('This website needs to be updated to use the CBPageHelpers::classNamesForPageTemplates().');
} else {
    $classNames = CBPagesPreferences::classNamesForPageTemplates();

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

CBView::render((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'pages',
]);

?>

<main class="CBUIRoot">
</main>

<?php

CBView::render((object)[
    'className' => 'CBAdminPageFooterView',
]);

CBHTMLOutput::render();

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

foreach (CBPagesPreferences::classNamesForEditableViews() as $className) {
    if (class_exists($editorClassName = "{$className}Editor")) {
        CBHTMLOutput::requireClassName($editorClassName);
    } else {
        if (is_callable($function = "{$className}::editorURLsForCSS")) {
            $URLs = call_user_func($function);
            array_walk($URLs, 'CBHTMLOutput::addCSSURL');
        }

        if (is_callable($function = "{$className}::editorURLsForJavaScript")) {
            $URLs = call_user_func($function);
            array_walk($URLs, function($URL) {
                CBHTMLOutput::addJavaScriptURL($URL);
            });
        }
    }
}

/**
 * Create the list of selectable views available to be added to the page.
 */

CBHTMLOutput::exportVariable('CBPageEditorAvailableViewClassNames', CBPagesPreferences::classNamesForAddableViews());

/**
 * @deprecated use kinds
 * Export page lists
 */

$listNames = CBViewPageLists::availableListNames();

CBHTMLOutput::exportVariable('CBPageEditorAvailablePageListClassNames', $listNames);

/**
 * Export page templates
 */

global $CBPageEditorAvailablePageTemplateClassNames;

if ($CBPageEditorAvailablePageTemplateClassNames || class_exists('CBPageTemplateList')) {
    throw new Exception('This website needs to be updated to use the CBViewPageTemplates class.');
} else {
    foreach (CBViewPageTemplates::availableTemplateClassNames() as $pageTemplateClassName) {
        $descriptor             = new stdClass();
        $descriptor->modelJSON  = json_encode($pageTemplateClassName::model());
        $descriptor->title      = $pageTemplateClassName::title();

        CBHTMLOutput::exportListItem('CBPageTemplateDescriptors', $pageTemplateClassName, $descriptor);
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

$selectedMenuItemID     = 'pages';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main class="CBSystemFont CBUIRoot">
</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

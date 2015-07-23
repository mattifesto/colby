<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

if (!isset($_GET['data-store-id']))
{
    $dataStoreID = Colby::random160();

    header("Location: /admin/pages/edit/?data-store-id={$dataStoreID}");

    exit;
}


CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Page Editor');
CBHTMLOutput::setDescriptionHTML('This is an app for editing pages.');

CBHTMLOutput::addCSSURL(CBSystemURL . '/css/standard.css');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin,pages,edit.css');

/**
 * 2015.07.23 This section is created as a place to include known necessary
 * JavaScript files for the editor itself (not view editors). Every file added
 * should include a brief description of why it is needed.
 */

/* This is still used by CBPageEditor.js, but it is deprecated. */
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBDelayTimer.js');

/* This is used by CBSpecArrayEditorFactory.js. */
CBHTMLOutput::addCSSURL(       CBSystemURL . '/javascript/CBEditorWidget.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBEditorWidgetFactory.js');

/* This renders the page editor. */
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageEditor.js');

/* CBPageEditor.js uses this to render the general page info editor. */
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageInformationEditorView.js');
CBHTMLOutput::addCSSURL(       CBSystemURL . '/javascript/CBPageInformationEditorView.css');

/* CBPageEditor.js uses this as the root spec list editor. */
CBHTMLOutput::addCSSURL(        CBSystemURL . '/javascript/CBSpecArrayEditor.css');
CBHTMLOutput::addJavaScriptURL( CBSystemURL . '/javascript/CBSpecArrayEditorFactory.js');

/* CBPageInformationEditorView.js uses these files and their code should
   be integrated into that file. When we move to using the model editor this
   code will all be integrated into a single CBViewPage editor. */
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageURIControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPublicationControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSelectionControl.js');

include CBSystemDirectory . '/sections/admin-page-settings.php';

$pagesPreferences           = CBModels::fetchModelByID(CBPagesPreferences::ID);

/**
 * Include all of the supported views.
 */

$supportedViewClassNames = $pagesPreferences->supportedViewClassNames;

/**
 * @deprecated use preferences instead of CBViewPageViews
 */
if (class_exists('CBViewPageViews')) {
    $supportedViewClassNames = array_values(array_unique(array_merge(
        $supportedViewClassNames, CBViewPageViews::availableViewClassNames()
    )));
}

foreach ($supportedViewClassNames as $className) {
    $function = "{$className}::editorURLsForCSS";

    if (is_callable($function)) {
        $URLs = call_user_func($function);
        array_walk($URLs, 'CBHTMLOutput::addCSSURL');
    }

    $function = "{$className}::editorURLsForJavaScript";

    if (is_callable($function)) {
        $URLs = call_user_func($function);
        array_walk($URLs, function($URL) {
            CBHTMLOutput::addJavaScriptURL($URL);
        });
    }
}

/**
 * Create the list of selectable views available to be added to the page.
 */

$selectableViewClassNames = $pagesPreferences->selectableViewClassNames;

/**
 * @deprecated use preferences instead of CBViewPageViews
 */
if (class_exists('CBViewPageViews')) {
    if (!is_callable($function = 'CBViewPageViews::selectableViewClassNames')) {
        $function = 'CBViewPageViews::availableViewClassNames';
    }

    $selectableViewClassNames = array_values(array_unique(array_merge(
        $selectableViewClassNames, call_user_func($function)
    )));
}

CBHTMLOutput::exportVariable('CBPageEditorAvailableViewClassNames', $selectableViewClassNames);

/**
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

<main>
</main>

<?php

$sql = <<<EOT

    SELECT
        `user`.`ID`,
        `user`.`facebookName` as `name`
    FROM
        `ColbyUsers` AS `user`
    JOIN
        `ColbyUsersWhoAreAdministrators` AS `administrator`
    ON
        `user`.`ID` = `administrator`.`userID`

EOT;

$result = Colby::query($sql);

$users = array();

while ($object = $result->fetch_object())
{
    $users[] = $object;
}

$result->free();

CBHTMLOutput::exportVariable('CBUsersWhoAreAdministrators', $users);
CBHTMLOutput::exportVariable('CBCurrentUserID', ColbyUser::currentUserId());

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

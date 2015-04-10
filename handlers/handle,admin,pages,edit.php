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
 * 2014.05.03 These files were originally included here because there was no way
 * to include JavaScript or CSS dependencies for editors. Now the correct way
 * to do this is to specify and editor initializer for an editor which can
 * include JavaScript and CSS dependencies.
 *
 * As the various editors take advantage of editor initializers, these includes
 * should be removed from this file.
 */

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Colby.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBContinuousAjaxRequest.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBCheckboxControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBDelayTimer.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBFileLinkControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBModelArrayEditor.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageEditor.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageInformationEditorView.js');
CBHTMLOutput::addCSSURL(       CBSystemURL . '/javascript/CBPageInformationEditorView.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageURIControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPublicationControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSectionEditorView.js');
CBHTMLOutput::addCSSURL(       CBSystemURL . '/javascript/CBSectionEditorView.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBViewMenu.js');
CBHTMLOutput::addCSSURL(       CBSystemURL . '/javascript/CBViewMenu.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSelectionControl.js');

include CBSystemDirectory . '/sections/admin-page-settings.php';

/**
 * Export views available to the editor
 */

foreach (CBViewPageViews::availableViewClassNames() as $className) {
    $function = "{$className}::includeEditorDependencies";

    if (is_callable($function)) {
        call_user_func($function);
    }
}

CBHTMLOutput::exportVariable('CBPageEditorAvailableViewClassNames',
                             CBViewPageViews::availableViewClassNames());

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

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
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageEditor.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageInformationEditorView.js');
CBHTMLOutput::addCSSURL(       CBSystemURL . '/javascript/CBPageInformationEditorView.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageURIControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPublicationControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSectionEditorView.js');
CBHTMLOutput::addCSSURL(       CBSystemURL . '/javascript/CBSectionEditorView.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSectionListView.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSectionSelectionControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSelectionControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBTextControl.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBTextAreaControl.js');

include CBSystemDirectory . '/sections/admin-page-settings.php';

include Colby::findFile('page-editor-configuration.php');

/**
 * Export section data
 */


global $CBPageEditorAvailableViewClassNames;

if (!$CBPageEditorAvailableViewClassNames)
{
    $CBPageEditorAvailableViewClassNames = array();
}

foreach ($CBPageEditorAvailableViewClassNames as $className)
{
    $className::includeEditorDependencies();
}

CBHTMLOutput::exportVariable('CBPageEditorAvailableViewClassNames', $CBPageEditorAvailableViewClassNames);


global $CBSections;

foreach ($CBSections as $sectionTypeID => $phpDescriptor)
{
    $javaScriptDescriptor               = new stdClass();
    $javaScriptDescriptor->name         = $phpDescriptor->name;
    $javaScriptDescriptor->modelJSON    = $phpDescriptor->modelJSON;

    /**
     * Originally section types would declare a constant which would be exported
     * here to be used only once by the `editor.js` file.  But newer sections
     * don't declare a constant and it's okay that they hardcode the section
     * type ID in their `editor.js` file. This code should eventually go away
     * as all sections move to the newer model.
     */

    $constantName = "{$phpDescriptor->name}SectionTypeID";

    if (defined($constantName))
    {
        CBHTMLOutput::exportConstant($constantName);
    }

    /**
     *
     */

    CBHTMLOutput::exportListItem('CBSectionDescriptors', $sectionTypeID, $javaScriptDescriptor);

    if ($phpDescriptor->URLForEditorCSS)
    {
        CBHTMLOutput::addCSSURL($phpDescriptor->URLForEditorCSS);
    }

    if ($phpDescriptor->URLForEditorJavaScript)
    {
        CBHTMLOutput::addJavaScriptURL($phpDescriptor->URLForEditorJavaScript);
    }

    if (isset($phpDescriptor->editorInitializer))
    {
        include_once $phpDescriptor->editorInitializer;
    }
}

/**
 * Export page template data
 */

global $CBPageTemplates;

foreach ($CBPageTemplates as $pageTemplateID => $descriptor)
{
    CBHTMLOutput::exportListItem('CBPageTemplateDescriptors', $pageTemplateID, $descriptor);
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

<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';

CBHTMLOutput::setTitleHTML('Page Editor');
CBHTMLOutput::setDescriptionHTML('This is an app for editing pages.');
CBHTMLOutput::begin();


if (ColbyUser::current()->isOneOfThe('Administrators'))
{
    CBHTMLOutput::addCSSURL(COLBY_SYSTEM_URL . '/handlers/handle,admin,pages,edit.css');

    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Colby.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/ColbySheet.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBContinuousAjaxRequest.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBCheckboxControl.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBFileLinkControl.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageEditor.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageInformationEditorView.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPageURIControl.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBPublicationControl.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSectionEditorView.js');
    CBHTMLOutput::addCSSURL(       CBSystemURL . '/javascript/CBSectionEditorView.css');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSectionListView.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSectionSelectionControl.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBSelectionControl.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBTextControl.js');
    CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBTextAreaControl.js');

    CBHTMLOutput::exportVariable('CBURLQueryVariables', $_GET);

    include Colby::findFile('page-editor-configuration.php');
}
else
{
    include Colby::findFile('snippets/authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/equalize.php';

$selectedMenuItemID     = 'pages';

include CBSystemDirectory . '/sections/admin-page-menu.php';


if (isset($_GET['data-store-id']))
{
    // load page data model for editing
}
else
{
    $dataStoreID = Colby::uniqueSHA1Hash();

    header("Location: /admin/pages/edit/?data-store-id={$dataStoreID}");
}

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


done:

CBHTMLOutput::render();

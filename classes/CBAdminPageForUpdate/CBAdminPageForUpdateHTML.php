<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$classURL = CBSystemURL . '/classes/CBAdminPageForUpdate';

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Update');
CBHTMLOutput::setDescriptionHTML('Tools to perform site version updates.');

CBHTMLOutput::addJavaScriptURL("{$classURL}/CBAdminPageForUpdate.js");

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'develop';
$spec->selectedSubmenuItemName  = 'update';

CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

$head = CPView::specForClassName('CPAdminSectionHeaderView');
$head->title = 'Update';

?>

<main>
    <?php CPView::renderAsHTML(CPView::compile($head)) ?>

    <div style="margin: 50px 0px; text-align: center;">
        <progress id="progress"
                  value="0"
                  style="width: 100px;"></progress>
    </div>

    <div style="text-align: center;">
        <button onclick="ColbySiteUpdater.update(this);">Update Site</button>
    </div>
</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();

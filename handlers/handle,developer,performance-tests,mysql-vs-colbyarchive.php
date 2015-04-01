<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Performance: MySQL vs ColbyArchive');
CBHTMLOutput::setDescriptionHTML('Test the relative performance of MySQL vs ColbyArchive.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,developer,performance-tests,mysql-vs-colbyarchive.js');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'test';
$spec->selectedSubmenuItemName  = 'performance-tests';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

?>

<main>
    <fieldset>
        <section class="control">
            <header>Performance Test: MySQL vs ColbyArchive</header>
            <div style="padding: 5px;">
                <button onclick="CPTMySQLvsColbyArchive.run();">Run Test</button>
                <progress id="progress"
                          value="0"
                          style="width: 150px; margin-left: 150px; vertical-align: middle;"></progress>
            </div>
        </section>
        <section class="control" style="margin-top: 10px;">
            <header>Status</header>
            <textarea id="status" style="min-height:400px;"></textarea>
        </section>
    </fieldset>
</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();

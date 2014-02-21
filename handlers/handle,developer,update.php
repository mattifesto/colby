<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Update');
CBHTMLOutput::setDescriptionHTML('Tools to perform site version updates.');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,develop,test-pages.js');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'develop';
$selectedSubmenuItemID  = 'update';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>
    <h1>Update</h1>

    <div style="margin: 50px 0px; text-align: center;">
        <progress id="progress"
                  value="0"
                  style="width: 100px;"></progress>
    </div>

    <div style="text-align: center;">
        <button onclick="ColbySiteUpdater.update(this);">Update Site</button>
    </div>
</main>

<script src="<?php echo Colby::findHandler('handle,developer,update.js', Colby::returnURL); ?>"></script>

<?php

include CBSystemDirectory . '/sections/admin-page-footer.php';

done:

CBHTMLOutput::render();

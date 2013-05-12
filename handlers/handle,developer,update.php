<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Update';
$page->descriptionHTML = 'Tools to perform site version updates.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

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

done:

$page->end();

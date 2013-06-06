<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Import Product Data';
$page->descriptionHTML = 'Import a data file to refresh the product data.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

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

<script src="<?php echo Colby::findHandler('handle,developer,performance-tests,mysql-vs-colbyarchive.js',
                                           Colby::returnURL); ?>"></script>

<?php

done:

$page->end();

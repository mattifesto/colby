<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Performance: MySQL vs ColbyArchive');
CBHTMLOutput::setDescriptionHTML('Test the relative performance of MySQL vs ColbyArchive.');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'test';
$selectedSubmenuItemID  = 'performance-tests';

include CBSystemDirectory . '/sections/admin-page-menu.php';

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

include CBSystemDirectory . '/sections/admin-page-footer.php';

done:

CBHTMLOutput::render();

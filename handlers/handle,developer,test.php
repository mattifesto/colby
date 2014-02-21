<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Colby Unit Tests');
CBHTMLOutput::setDescriptionHTML('Developer tests to make sure there are no regressions in functionality.');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Tests.js');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'test';
$selectedSubmenuItemID  = 'test';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<style>
input[type=text]
{
    width: 400px;
    padding: 2px;
}

dd
{
    margin: 5px 0px 15px;
}
</style>

<?php

if (!COLBY_MYSQL_HOST)
{
    ?>

    <p>Please finish setting up the colby-configuration.php file and return to this page.

    <?php
}
else
{
    ?>

    <p>This file was loaded: <span class="time" data-timestamp="<?php echo time() * 1000; ?>"></span>
    <p><button onclick="doRunUnitTests();">Run unit tests</button>
    <progress id="ajax-communication" value="0"></progress>

    <p><button onclick="doRunJavascriptUnitTests();">Run Javascript unit tests</button>

    <?php
}

?>

<div id="error-log"></div>

<?php

include CBSystemDirectory . '/sections/admin-page-footer.php';

done:

CBHTMLOutput::render();

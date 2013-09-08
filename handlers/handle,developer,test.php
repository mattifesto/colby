<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Unit Tests';
$page->descriptionHTML = 'Develeloper tests to make sure there are no regressions in functionality.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

?>

<script src="<?php echo COLBY_SYSTEM_URL . '/javascript/Tests.js'; ?>"></script>

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

done:

$page->end();


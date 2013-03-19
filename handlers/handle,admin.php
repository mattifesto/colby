<?php

include(Colby::findHandler('handle-ensure-installation.php'));

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Site Administration';
$page->descriptionHTML = 'Edit the settings and content of this website.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

?>

<style scoped>

section.widget
{
    display: inline-block;
    width: 300px;
    min-height: 100px;
    margin: 5px;
    border: 1px solid #dddddd;
    vertical-align: top;
}

section.widget > div
{
    padding: 5px;
}

section.widget > header
{
    padding-top: 5px;
    padding-bottom: 7px;
    background-color: #333333;
    color: #bbbbbb;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
}

</style>

<?php

$adminWidgetFilenames = Colby::globSnippets('admin-widget-*.php');

foreach ($adminWidgetFilenames as $adminWidgetFilename)
{
    include $adminWidgetFilename;
}

done:

$page->end();

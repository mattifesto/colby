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

section.widget .version-numbers
{
    text-align: center;
}

section.widget .version-number
{
    display: inline-block;
    padding: 5px;
}

section.widget .version-number h1
{
    color: gray;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

section.widget .version-number .number
{
    margin-top: -10px;
    font-size: 40px;
    font-weight: bold;
}

section.widget .version-number + .version-number
{
    border-left: 1px solid #dddddd;
}

</style>

<?php

$adminWidgetFilenames = Colby::globSnippets('admin-widget-*.php');

foreach ($adminWidgetFilenames as $adminWidgetFilename)
{
    include $adminWidgetFilename;
}

if (ColbyUser::current()->isOneOfThe('Developers'))
{
    $iniValues = ini_get_all(null, false);

    ?>

    <section class="formatted-content standard-formatted-content">
        <h1>php.ini values</h1>
        <dl>

            <?php

            foreach ($iniValues as $key => $value)
            {
                if (empty($value))
                {
                    $value = '<no value>';
                }

                $keyHTML = ColbyConvert::textToHTML($key);
                $valueHTML = ColbyConvert::textToHTML($value);

                echo "<dt>{$keyHTML}</dt><dd>{$valueHTML}</dd>";
            }

            ?>

        </dl>
    </section>

    <?php
}

done:

$page->end();

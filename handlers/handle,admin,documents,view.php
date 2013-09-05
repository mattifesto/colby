<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Archive Details';
$page->descriptionHTML = 'View the contents of an archive.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$archiveId = $_GET['archive-id'];

$absoluteArchiveFilename = ColbyArchive::absoluteDataDirectoryForArchiveId($archiveId) .
                           "/archive.data";

$root = null;

if (is_file($absoluteArchiveFilename))
{
    $root = unserialize(file_get_contents($absoluteArchiveFilename));
}

$archiveTitleHTML = isset($root->data->titleHTML) ? $root->data->titleHTML : '';

?>

<main>
    <style>
        pre
        {
            margin-bottom: 50px;
        }

        h6
        {
            margin-bottom: 20px;
        }

        section.keys-and-values
        {
            margin-bottom: 30px;
        }

        section.keys-and-values dl
        {
            display: inline-block;
            margin: 10px;
            vertical-align: top;
        }

        section.keys-and-values dl dt
        {
            font-weight: bold;
        }
    </style>

    <header>
        <h1><?php echo $archiveId; ?></h1>
        <h2><?php echo $archiveTitleHTML; ?></h2>
    </header>

    <?php

    displayAttributesForRoot($root);

    displayDataForRoot($root);

    /**
     * Add a function to show any data not shown by the previous functions.
     */

    ?>

</main>

<?php

done:

$page->end();

/* ---------------------------------------------------------------- */

function displayAttributesForRoot($root)
{
    ?>

    <section class="keys-and-values attributes">
        <h1>Attributes</h1>

        <?php

        if (!isset($root->attributes))
        {
            echo '<p>This archive has no attributes.';
        }
        else
        {
            $keysWithTimeValues = array('created', 'modified');

            foreach ($root->attributes as $key => $value)
            {
                if (in_array($key, $keysWithTimeValues))
                {
                    $type = 'time';
                }
                else
                {
                    $type = null;
                }

                displayKeyValuePair($key, $value, $type);
            }
        }

        ?>

    </section>

    <?php
}

function displayDataForRoot($root)
{
    ?>

    <section class="keys-and-values data">
        <h1>Data</h1>

        <?php

        if (!isset($root->data))
        {
            echo '<p>This archive has no data.';
        }
        else
        {
            foreach ($root->data as $key => $value)
            {
                displayKeyValuePair($key, $value);
            }
        }

        ?>

    </section>

    <?php
}

function displayKeyValuePair($key, $value, $type = null)
{
    ?>

    <dl>
        <dt><?php echo ColbyConvert::textToHTML($key); ?><dt>
        <dd><?php

            if ('time' == $type)
            {
                displayValueForTime($value);
            }
            else
            {
                displayValue($value);
            }

        ?></dd>
    </dl>

    <?php
}

function displayValue($value)
{
    if (!is_scalar($value))
    {
        echo ColbyConvert::textToHTML(var_export($value, true));
    }
    else
    {
        echo ColbyConvert::textToHTML($value);
    }
}

function displayValueForTime($value)
{
    $javaScriptTime = $value * 1000;

    echo "<span class=\"time\" data-timestamp=\"{$javaScriptTime}\">{$value}</span>";
}

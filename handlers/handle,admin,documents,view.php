<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once CBSystemDirectory . '/snippets/shared/documents-administration.php';

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Archive Details');
CBHTMLOutput::setDescriptionHTML('View the contents of an archive.');

CBView::renderModelAsHTML((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'develop',
    'selectedSubmenuItemName' => 'documents',
]);

$ID = $_GET['archive-id'];

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

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
            margin: 15px 20px;
            vertical-align: top;
        }

        section.keys-and-values dl dt
        {
            font-weight: bold;
        }

        section.keys-and-values dl dd .hash
        {
            font-size: 60%;
        }

    </style>

    <header>
        <h1><?php echo $ID; ?></h1>
    </header>

    <?php

    renderColbyPagesRowForID($ID);

    renderDataStoreFileListForID($ID)

    /**
     * Add a function to show any data not shown by the previous functions.
     */

    ?>

    <div style="margin: 80px auto; text-align: center; width: 480px;">
        2015.08.02 This page used to have the ability to delete the page and its
        data store but it was removed for two reasons. First it is dangerous to
        remove data stores and the process should be thought out before enabling
        it again. Second, it used the ColbyDocument class which is being
        removed.
    </div>

</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();

/* ---------------------------------------------------------------- */

/**
 * @return void
 */
function renderColbyPagesRowForID($ID)
{
    $IDAsSQL    = ColbyConvert::textToSQL($ID);
    $sql        = <<<EOT

        SELECT
            `id`,
            LOWER(HEX(`archiveID`)) as `archiveID`,
            `URI`,
            `titleHTML`,
            `subtitleHTML`,
            `thumbnailURL`,
            `searchText`,
            `published`,
            `publishedBy`
        FROM
            `ColbyPages`
        WHERE
            `archiveId` = UNHEX('{$IDAsSQL}')

EOT;

    $result = Colby::query($sql);

    ?>

    <section class="keys-and-values colby-documents-table-row">
        <h1>ColbyPages Row Data</h1>

        <?php

        if ($result->num_rows != 1)
        {
            echo "<p>This ID does not represent a page.";
        }
        else
        {
            $row = $result->fetch_object();

            foreach ($row as $name => $value)
            {
                $type = null;

                if ('published' == $name)
                {
                    $type = 'time';
                }

                displayKeyValuePair($name, $value, $type);
            }
        }

        ?>

    </section>

    <?php

    $result->free();
}

/**
 * @return null
 */
function renderDataStoreFileListForID($ID) {
    $directory = CBDataStore::directoryForID($ID);

    if (!is_dir($directory)) {
        return;
    }

    $list   = array();
    $handle = opendir($directory);

    while (false !== ($filename = readdir($handle))) {
        if (is_dir($filename)) {
            $filename = "{$filename} (directory)";
        }

        $list[] = $filename;
    }

    echo '<section style="background-color: hsl(30, 50%, 95%); width: 500px; margin: 0 auto; padding: 5px 20px 20px;">',
         '<h1 style="margin-bottom: 20px; text-align: center;">Data Store Directory Listing</h1>';

    foreach ($list as $filename) {
        $filenameAsHTML = ColbyConvert::textToHTML($filename);
        echo "<p><code>{$filenameAsHTML}</code>";
    }

    echo '</section>';
}

/**
 * @return void
 */
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

/**
 * @return void
 */
function displayValue($value)
{
    if (!is_scalar($value))
    {
        echo ColbyConvert::textToHTML(var_export($value, true));
    }
    else if (is_string($value) &&
             preg_match('/^[0-9a-fA-F]{40}$/', $value))
    {
        /**
         * This appears to be a sha1 hash.
         */

        echo "<span class=\"hash\">{$value}</span>";

    }
    else
    {
        echo ColbyConvert::textToHTML($value);
    }
}

/**
 * @return void
 */
function displayValueForTime($value)
{
    if (is_numeric($value))
    {
        $javaScriptTime = $value * 1000;

        echo "<span class=\"time\" data-timestamp=\"{$javaScriptTime}\">{$value}</span>";
    }
    else
    {
        displayValue($value);
    }
}

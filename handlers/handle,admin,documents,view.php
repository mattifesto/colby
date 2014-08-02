<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Archive Details');
CBHTMLOutput::setDescriptionHTML('View the contents of an archive.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('develop');
$menu->setSelectedSubmenuItemName('documents');
$menu->renderHTML();

$archiveId                  = $_GET['archive-id'];
$absoluteArchiveFilename    = ColbyArchive::absoluteDataDirectoryForArchiveId($archiveId) .
                              '/archive.data';
$root                       = null;

if (is_file($absoluteArchiveFilename))
{
    $root = unserialize(file_get_contents($absoluteArchiveFilename));
}

$archiveTitleHTML = isset($root->data->titleHTML) ? $root->data->titleHTML : '';

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
        <h1><?php echo $archiveId; ?></h1>
        <h2><?php echo $archiveTitleHTML; ?></h2>
    </header>

    <?php

    displayColbyDocumentsTableRow($archiveId);

    displayAttributesForRoot($root);

    displayDataForRoot($root);

    /**
     * Add a function to show any data not shown by the previous functions.
     */

    ?>

    <div style="margin-top: 80px; text-align: center;">

        <input type="hidden" id="archive-id" value="<?php echo $archiveId; ?>">

        <div>
            <input type="text"
                   id="archive-id-for-confirmation"
                   placeholder="Enter the document's archive id to enable deletion"
                   class="big-field"
                   style="width: 400px; text-align: center;">
        </div>

        <div style="margin-top: 20px;">
            <a class="big-button"
               onclick="ColbyDocumentDeleter.deleteDocument();">

               Delete this document
            <a>
        </div>
    </div>

</main>

<script src="<?php echo COLBY_SYSTEM_URL; ?>/handlers/handle,admin,documents,view.js"></script>

<?php

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

CBHTMLOutput::render();

/* ---------------------------------------------------------------- */

/**
 * @return void
 */
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

/**
 * @return void
 */
function displayColbyDocumentsTableRow($archiveId)
{
    $archiveIdForSQL = Colby::mysqli()->escape_string($archiveId);

$sql = <<<EOT
SELECT
    `id`,
    LOWER(HEX(`archiveID`)) as `archiveID`,
    LOWER(HEX(`groupID`)) as `groupID`,
    LOWER(HEX(`typeID`)) as `typeID`,
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
    `archiveId` = UNHEX('{$archiveIdForSQL}');
EOT;

    $result = Colby::query($sql);

    ?>

    <section class="keys-and-values colby-documents-table-row">
        <h1>ColbyDocuments Table Row</h1>

        <?php

        if ($result->num_rows != 1)
        {
            echo "<p>There is no row for this document.";
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
 * @return void
 */
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

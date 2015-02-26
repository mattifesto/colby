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

    <div style="margin-top: 80px; text-align: center;">

        <input type="hidden" id="archive-id" value="<?= $ID ?>">

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

<script src="<?= CBSystemURL ?>/handlers/handle,admin,documents,view.js"></script>

<?php

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

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
 * @return void
 */
function renderDataStoreFileListForID($ID) {
    $dataStore = new CBDataStore($ID);

    if (!is_dir($dataStore->directory())) {
        return;
    }

    $list   = array();
    $handle = opendir($dataStore->directory());

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

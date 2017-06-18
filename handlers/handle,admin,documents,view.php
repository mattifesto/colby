<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once CBSystemDirectory . '/snippets/shared/documents-administration.php';

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('ID Info');
CBHTMLOutput::setDescriptionHTML('View the contents of an archive.');
CBHTMLOutput::requireClassName('CBAdminPageForID');

CBView::renderModelAsHTML((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'develop',
    'selectedSubmenuItemName' => 'documents',
]);

if (empty($_GET['ID'])) {
    // @deprecated use `ID` instead of `archive-id`
    $ID = $_GET['archive-id'];
} else {
    $ID = $_GET['ID'];
}

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

<main class="CBAdminPageForID" style="flex: 1 1 auto;">
    <header>
        <h1>ID<span><?php echo $ID; ?></span></h1>
    </header>

    <?php

    CBAdminPageForID::renderCBModelsInformation($ID);
    CBAdminPageForID::renderCBImagesInformation($ID);

    renderColbyPagesRowForID($ID);

    renderDataStoreFileListForID($ID)

    ?>
</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();

/* ---------------------------------------------------------------- */

/**
 * @return null
 */
function renderColbyPagesRowForID($ID) {
    $IDAsSQL    = ColbyConvert::textToSQL($ID);
    $SQL        = <<<EOT

        SELECT
            `id`,
            LOWER(HEX(`archiveID`)) as `archiveID`,
            `className`,
            `classNameForKind`,
            `created`,
            `iteration`,
            `modified`,
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

    $row = CBDB::SQLToObject($SQL);


    if ($row) {
        echo '<section class="ColbyPages"><h1>ColbyPages Table</h1>';

        foreach ($row as $name => $value) {
            $type = null;

            if ('published' == $name || 'created' == $name || 'modified' == $name) {
                $type = 'time';
            } else if ('thumbnailURL' == $name) {
                $type = 'URI';
            }

            displayKeyValuePair($name, $value, $type);
        }

        echo '</section>';
    }
}

/**
 * @return null
 */
function renderDataStoreFileListForID($ID) {
    $directory = CBDataStore::directoryForID($ID);

    if (!is_dir($directory)) {
        return;
    }

    ?>

    <section>
    <div style="background-color: hsl(30, 50%, 95%); width: 500px; margin: 0 auto; padding: 5px 20px 20px;">
        <h1 style="margin-bottom: 20px; text-align: center;">Data Store Directory Listing</h1>

        <?php

        $iterator = new RecursiveDirectoryIterator($directory);

        while ($iterator->valid()) {
            if ($iterator->isFile()) {
              $subpathname = $iterator->getSubPathname();
              $subpathnameAsHTML = cbhtml($subpathname);
              $URLAsHTML = cbhtml(CBDataStore::flexpath($ID, $subpathname, CBSiteURL));

              echo "<p><code><a href='{$URLAsHTML}'>{$subpathnameAsHTML}</a></code>";
            }

            $iterator->next();
        }

        ?>

    </div>
    </section>

    <?php
}

/**
 * @return void
 */
function displayKeyValuePair($key, $value, $type = null) {
    ?>

    <dl>
        <dt><?php echo ColbyConvert::textToHTML($key); ?><dt>
        <dd><?php

            if ('time' == $type) {
                displayValueForTime($value);
            } else if ('URI' == $type) {
                displayValueForURI($value);
            } else {
                displayValue($value);
            }

        ?></dd>
    </dl>

    <?php
}

/**
 * @return null
 */
function displayValue($value) {
    if (!is_scalar($value)) {
        echo ColbyConvert::textToHTML(var_export($value, true));
    } else if (is_string($value) && preg_match('/^[0-9a-fA-F]{40}$/', $value)) {
        /**
         * This appears to be a sha1 hash.
         */

        echo "<span class=\"hash\">{$value}</span>";
    } else {
        echo ColbyConvert::textToHTML($value);
    }
}

/**
 * @return null
 */
function displayValueForTime($value) {
    if (is_numeric($value)) {
        $javaScriptTime = $value * 1000;

        echo "<span class=\"time\" data-timestamp=\"{$javaScriptTime}\">{$value}</span>";
    } else {
        displayValue($value);
    }
}

/**
 * @return null
 */
function displayValueForURI($value) {
    $valueAsHTML = cbhtml($value);

    echo "<span>{$valueAsHTML}</span>";

    if ($ID = CBDataStore::URIToID($value)) {
        $link = "/admin/documents/view/?ID={$ID}";
        echo "<br><a href=\"{$link}\">data store information</a>";
    }
}

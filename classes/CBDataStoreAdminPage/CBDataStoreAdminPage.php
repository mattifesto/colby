<?php

final class CBDataStoreAdminPage {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['develop', 'datastores'];
    }

    /**
     * @return object
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        $ID = cb_query_string_value('ID');

        CBPageContext::push([
            'titleAsHTML' => 'Data Store Inspector',
            'descriptionAsHTML' => $ID,
        ]);

        CBView::render((object)[
            'className' => 'CBPageTitleAndDescriptionView',
        ]);

        $specAndModel = CBModels::fetchSpecAndModelByID($ID);

        if ($specAndModel && $specAndModel->spec->className === "CBImage") {
            CBView::render((object)[
                'className' => 'CBArtworkView',
                'CSSClassNames' => ['hideSocial'],
                'image' => $specAndModel->model,
            ]);
        }

        CBDataStoreAdminPage::renderCBModelsInformation($specAndModel);
        CBDataStoreAdminPage::renderCBImagesInformation($ID);
        CBDataStoreAdminPage::renderArchiveInformation($ID);
        CBDataStoreAdminPage::renderColbyPagesRowForID($ID);
        CBDataStoreAdminPage::renderDataStoreFileListForID($ID);

        ?><div class="CBDataStoreAdminPage_delete" data-id="<?= $ID ?>"></div><?php

    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUIExpander', 'Colby'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
    }

    /**
     * @param hex160 $ID
     *
     * @return null
     */
    static function renderArchiveInformation($ID) {
        if (!class_exists('ColbyArchive')) {
            return;
        }

        $filepath = CBDataStore::flexpath($ID, 'archive.data', cbsitedir());

        if (!is_file($filepath)) {
            return;
        }

        $archive = ColbyArchive::open($ID);
        $message = "Archive\n\n" .
            var_export($archive->data(), true);
        $message = cbhtml(json_encode($message));

        ?>

        <div class="CBUIExpander_builder" data-message="<?= $message ?>"></div>

        <?php
    }

    /**
     * @return null
     */
    static function renderCBImagesInformation($ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT  `created`, `modified`, `extension`
            FROM    `CBImages`
            WHERE   `ID` = {$IDAsSQL}

EOT;

        if ($row = CBDB::SQLToObject($SQL)) {
            $message = "CBImages Row\n\n" .
                json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $message = cbhtml(json_encode($message));

            ?>

            <div class="CBUIExpander_builder" data-message="<?= $message ?>"></div>

            <?php
        }
    }

    /**
     * @param object|false $data
     *
     *      {
     *          spec: object
     *          model: object
     *      }
     *
     * @return null
     */
    static function renderCBModelsInformation($data) {
        if ($data === false) {
            return;
        }

        $message = "CBModels Spec\n\n" .
            json_encode($data->spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $message = cbhtml(json_encode($message));

        ?>

        <div class="CBUIExpander_builder" data-message="<?= $message ?>"></div>

        <?php

        $message = "CBModels Model\n\n" .
            json_encode($data->model, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $message = cbhtml(json_encode($message));

        ?>

        <div class="CBUIExpander_builder" data-message="<?= $message ?>"></div>

        <?php
    }

    /**
     * @return null
     */
    static function renderColbyPagesRowForID($ID) {
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

        if ($row = CBDB::SQLToObject($SQL)) {
            $message = "ColbyPages Row\n\n" .
                json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $message = cbhtml(json_encode($message));

            ?>

            <div class="CBUIExpander_builder" data-message="<?= $message ?>"></div>

            <?php
        }
    }

    /**
     * @return null
     */
    static function renderDataStoreFileListForID($ID) {
        $directory = CBDataStore::directoryForID($ID);

        if (!is_dir($directory)) {
            return;
        }

        $links = [];
        $iterator = new RecursiveDirectoryIterator($directory);

        while ($iterator->valid()) {
            if ($iterator->isFile()) {
              $subpathname = $iterator->getSubPathname();
              $URL = CBDataStore::flexpath($ID, $subpathname, cbsiteurl());

              $links[] = (object)[
                  'text' => $subpathname,
                  'URI' => $URL,
              ];
            }

            $iterator->next();
        }

        if (!empty($links)) {
            $links = array_map(function ($link) {
                return "{$link->text}\n{$link->URI}";
            }, $links);

            $message = "Data Store Files\n\n" .
                implode("\n\n", $links);
            $message = cbhtml(json_encode($message));

            ?>

            <div class="CBUIExpander_builder" data-message="<?= $message ?>"></div>

            <?php
        }
    }
}

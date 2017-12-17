<?php

final class CBModelInspector {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath() {
        return ['models', 'inspector'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::setTitleHTML('Inspector');
    }

    /**
     * @param object $args
     *
     *      {
     *          ID: hex160
     *      }
     *
     * @return object
     *
     *      {
     *          className: "CBModelInspector_modelData"
     *          versions: [object]
     *      }
     */
    static function CBAjax_fetchModelData(stdClass $args) {
        $ID = CBModel::value($args, 'ID', null, 'CBConvert::valueAsHex160');

        if (!CBHex160::is($ID)) {
            throw new InvalidArgumentException('ID');
        }

        $object = (object)[
            'className' => 'CBModelInspector_fetchModelData',
        ];

        $object->archive = CBModelInspector::fetchArchive($ID);
        $object->dataStoreFiles = CBModelInspector::fetchDataStoreFiles($ID);
        $object->modelVersions = CBModelInspector::fetchModelVersions($ID);
        $object->rowFromCBImages = CBModelInspector::fetchRowFromCBImages($ID);
        $object->rowFromColbyPages = CBModelInspector::fetchRowFromColbyPages($ID);

        return $object;
    }

    /**
     * @return string
     */
    static function CBAjax_fetchModelData_group() {
        return 'Administrators';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBArtworkElement', 'CBMessageMarkup', 'CBUI', 'CBUIExpander', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v359.js', cbsysurl())];
    }

    /**
     * @return [[key, value]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $ID = cb_query_string_value('ID');
        return [
            ['CBModelInspector_modelID', $ID],
        ];
    }

    /**
     * @param hex160 $ID
     *
     * @return string
     */
    private static function fetchArchive(string $ID): string {
        if (!class_exists('ColbyArchive')) {
            return '';
        }

        $filepath = CBDataStore::flexpath($ID, 'archive.data', cbsitedir());

        if (!is_file($filepath)) {
            return '';
        }

        $archive = ColbyArchive::open($ID);

        return var_export($archive->data(), true);
    }

    /**
     * @param hex160 $ID
     *
     * @return [object]
     */
    private static function fetchDataStoreFiles(string $ID): array {
        $directory = CBDataStore::directoryForID($ID);
        $files = [];

        if (!is_dir($directory)) {
            return $files;
        }

        $iterator = new RecursiveDirectoryIterator($directory);

        while ($iterator->valid()) {
            if ($iterator->isFile()) {
              $subpathname = $iterator->getSubPathname();
              $URL = CBDataStore::flexpath($ID, $subpathname, cbsiteurl());

              $files[] = (object)[
                  'text' => $subpathname,
                  'URL' => $URL,
              ];
            }

            $iterator->next();
        }

        return $files;
    }

    /**
     * @param hex160 $ID
     *
     * @return [object]
     */
    private static function fetchModelVersions(string $ID): array {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT      `version`, `timestamp`, `specAsJSON`, `modelAsJSON`
            FROM        `CBModelVersions`
            WHERE       `ID` = {$IDAsSQL}
            ORDER BY    `version` DESC

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * @param hex160 $ID
     *
     * @return ?stdClass
     */
    private static function fetchRowFromCBImages(string $ID): ?stdClass {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT  `created`, `modified`, `extension`
            FROM    `CBImages`
            WHERE   `ID` = {$IDAsSQL}

EOT;

        $result = CBDB::SQLToObject($SQL);

        if ($result === false) {
            return null;
        } else {
            return $result;
        }
    }

    /**
     * @param hex160 $ID
     *
     * @return ?stdClass
     */
    private static function fetchRowFromColbyPages(string $ID): ?stdClass {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT  `id`,
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
                    `publishedBy`,
                    `keyValueData`
            FROM    `ColbyPages`
            WHERE   `archiveId` = {$IDAsSQL}

EOT;

        $result = CBDB::SQLToObject($SQL);

        if ($result === false) {
            return null;
        } else {
            $result->keyValueData = json_decode($result->keyValueData);
            return $result;
        }
    }
}

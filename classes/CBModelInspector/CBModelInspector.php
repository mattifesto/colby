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
        CBHTMLOutput::setTitleHTML('Model Inspector');
        CBHTMLOutput::setDescriptionHTML('View information about a model.');
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
     *          versions: [object]
     *      }
     */
    static function CBAjax_fetchModelData(stdClass $args) {
        $ID = CBModel::value($args, 'ID', null, 'CBConvert::valueAsHex160');

        if (empty($ID)) {
            throw new InvalidArgumentException('ID');
        }

        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT      `version`, `timestamp`, `specAsJSON`, `modelAsJSON`
            FROM        `CBModelVersions`
            WHERE       `ID` = {$IDAsSQL}
            ORDER BY    `version` DESC

EOT;

        $rowSQL = <<<EOT

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

        $row = CBDB::SQLToObject($rowSQL);
        $row->keyValueData = json_decode($row->keyValueData);

        return (object)[
            'versions' => CBDB::SQLToObjects($SQL),
            'row' => $row,
        ];
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
        return ['CBMessageMarkup', 'CBUI', 'CBUIExpander', 'CBUIStringEditor'];
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
}

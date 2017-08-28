<?php

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response   = new CBAjaxResponse();
$partIndex  = (int)$_POST['part-index'];
$filepath   = CBDataStore::directoryForID(CBPagesAdministrationDataStoreID) . '/data.json';

if (0 == $partIndex || !is_file($filepath)) {
    $data                           = new stdClass();
    $data->dataStoresWithoutPages   = array();
    $data->pagesWithoutDataStores   = array();
} else {
    $data = json_decode(file_get_contents($filepath));
}

DataStoreExplorer::explorePart($partIndex, $data);

CBDataStore::makeDirectoryForID(CBPagesAdministrationDataStoreID);
file_put_contents($filepath, json_encode($data));

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = 'Part explored.';

$response->send();

/* ---------------------------------------------------------------- */

class DataStoreExplorer {

    /**
     * Returns an array of data store IDs that have existing data store
     * directories.
     *
     * @return array
     */
    static function dataStoreIDsForPart($hexPartIndex) {
        $dataStoreIDs           = array();
        $dataStoreDirectories   = glob(CBSiteDirectory . "/data/{$hexPartIndex}/*/*");

        foreach ($dataStoreDirectories as $directory) {
            if (!preg_match('/([0-9a-f]{2})\/([0-9a-f]{2})\/([0-9a-f]{36})/', $directory, $matches)) {
                throw new RuntimeException("The data store directory `{$directory}` is incorrectly named. " .
                    "Investigate and remove the directory manually to continue.");
            }

            $dataStoreID    = $matches[1] . $matches[2] . $matches[3];
            $dataStoreIDs[] = $dataStoreID;
        }

        return $dataStoreIDs;
    }

    /**
     * @return void
     */
    static function explorePart($partIndex, $data)
    {
        $hexPartIndex   = sprintf('%02x', $partIndex);
        $dataStoreIDs   = self::dataStoreIDsForPart($hexPartIndex);
        $pageIDs        = self::pageIDsForPart($hexPartIndex);

        $dataStoresWithoutPages         = array_diff($dataStoreIDs, $pageIDs);
        $data->dataStoresWithoutPages   = array_merge($data->dataStoresWithoutPages,
                                                      $dataStoresWithoutPages);

        $pagesWithoutDataStores         = array_diff($pageIDs, $dataStoreIDs);
        $data->pagesWithoutDataStores   = array_merge($data->pagesWithoutDataStores,
                                                      $pagesWithoutDataStores);
    }

    /**
     * Returns an array of IDs for existing pages fot the part index.
     *
     * @return array
     */
    static function pageIDsForPart($hexPartIndex) {

        /**
         * CONCAT has three parts:
         *
         *  '\\\\'
         *      This will evaluate to '\\' in the SQL which will then evaluate to
         *      a single backslash which will escape the character that follows it
         *      which will be necessary if that character happens to be '%'.
         *
         *  UNHEX('{$hexPartIndex}')
         *      Since `hexPartIndex` is two hex characters this will evaluate to
         *      a single "binary character set" character.
         *
         *  '%'
         *      This percent is the wildcard character to be used in the context
         *      of the 'LIKE' keyword.
         */

        $sql = <<<EOT

            SELECT
                LOWER(HEX(`archiveId`)) AS `archiveId`
            FROM
                `ColbyPages`
            WHERE
                `archiveId` LIKE CONCAT('\\\\', UNHEX('{$hexPartIndex}'), '%')

EOT;

        $result     = Colby::query($sql);
        $pageIDs    = array();

        while ($row = $result->fetch_object()) {
            $pageIDs[] = $row->archiveId;
        }

        $result->free();

        return $pageIDs;
    }
}

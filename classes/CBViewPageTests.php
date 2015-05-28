<?php

final class CBViewPageTests {

    /**
     * @return null
     */
    public static function saveTest() {
        $ID                     = '697f4e4cb46436f5c204e495caff5957d4d62a31';
        $kind                   = 'CBViewPageTestPages';

        CBPages::deleteRowWithDataStoreID($ID);
        if (is_dir(CBDataStore::directoryForID($ID))) {
            CBDataStore::deleteForID(['ID' => $ID]);
        }

        $spec                   = CBViewPage::makeSpecForID(['ID' => $ID]);
        $spec->classNameForKind = $kind;
        $spec->isPublished      = true;
        $spec->URI              = 'CBViewPageTests/supercalifragilisticexpialidocious';

        CBViewPage::save(['spec' => $spec]);

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$ID}')");

        if ($count != 1) {
            throw new Exception('The page was not found when searching by `ID`.');
        }

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `ColbyPages` WHERE `classNameForKind` = '{$kind}'");

        if ($count != 1) {
            throw new Exception('The page was not found when searching by `classNameForKind`.');
        }

        $URI = CBDB::SQLToValue("SELECT `URI` FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$ID}')");

        if ($URI != $spec->URI) {
            $pu = json_encode($URI);
            $su = json_encode($spec->URI);
            throw new Exception("The page URI: {$pu} does not match the spec URI: {$su}.");
        }

        CBPages::deleteRowWithDataStoreID($ID);
        CBDataStore::deleteForID(['ID' => $ID]);
    }
}

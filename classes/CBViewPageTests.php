<?php

final class CBViewPageTests {

    /**
    @return null
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

        CBViewPage::save(['spec' => $spec]);

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$ID}')");

        if ($count != 1) {
            throw new Exception('Save page not found by ID.');
        }

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `ColbyPages` WHERE `classNameForKind` = '{$kind}'");

        if ($count != 1) {
            throw new Exception('Saved page not found by classNameForKind.');
        }

        CBPages::deleteRowWithDataStoreID($ID);
        CBDataStore::deleteForID(['ID' => $ID]);
    }
}

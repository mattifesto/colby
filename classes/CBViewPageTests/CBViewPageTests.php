<?php

final class CBViewPageTests {

    /**
     * @return null
     */
    static function saveTest() {
        $ID = '697f4e4cb46436f5c204e495caff5957d4d62a31';
        $kind = 'CBViewPageTestPages';
        $specURI = 'CBViewPageTests/super écali fragil isticø expialidociouså';
        $modelURI = 'cbviewpagetests/super-cali-fragil-istic-expialidocious';

        /**
         * @NOTE The building emoji will cause an error if the table is not
         * correctly updated. A heart emoji will not cause an error.
         */

        $title = 'I 🏛 <Websites>!';
        $titleAsHTMLExpected = 'I 🏛 &lt;Websites&gt;!';

        CBModels::deleteByID([$ID]);

        $spec = CBViewPage::fetchSpecByID($ID, true);
        $spec->title = $title;
        $spec->classNameForKind = $kind;
        $spec->isPublished = true;
        $spec->URI = $specURI;

        CBModels::save([$spec]);

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$ID}')");

        if ($count != 1) {
            throw new Exception('The page was not found when searching by `ID`.');
        }

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `ColbyPages` WHERE `classNameForKind` = '{$kind}'");

        if ($count != 1) {
            throw new Exception('The page was not found when searching by `classNameForKind`.');
        }

        $URI = CBDB::SQLToValue("SELECT `URI` FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$ID}')");

        if ($URI != $modelURI) {
            $pu = json_encode($URI);
            $su = json_encode($spec->URI);
            throw new Exception("The page URI: {$pu} does not match the spec URI: {$su}.");
        }

        $titleAsHTML = CBDB::SQLToValue("SELECT `titleHTML` FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$ID}')");

        if ($titleAsHTML !== $titleAsHTMLExpected) {
            throw new ErrorException("The ColbyPages titleHTML: '{$titleAsHTML}' does not match the expected value: '{$titleAsHTMLExpected}'");
        }

        CBModels::deleteByID([$ID]);
    }
}

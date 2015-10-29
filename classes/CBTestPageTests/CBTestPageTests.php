<?php

final class CBTestPageTests {

    /**
     * @return null
     */
    public static function test() {
        $ID = '1406f65b87c45a3927672cf3634a88d6daeca48b';
        $IDAsSQL = CBHex160::toSQL($ID);
        $countSQL = "SELECT COUNT(*) FROM `ColbyPages` WHERE `archiveID` = {$IDAsSQL}";

        CBModels::deleteModelsByID([$ID]);

        if (CBDB::SQLToValue($countSQL) !== '0') {
            throw new Exception('The test page already exists in the `ColbyPages` table.');
        }

        $spec = CBModels::modelWithClassName('CBTestPage', ['ID' => $ID]);
        $spec->description = 'A test page for page classes';
        $spec->published = time();
        $spec->title = 'Hello, world!';
        $spec->URIPath = 'hello-world';

        CBModels::save([$spec]);

        if (CBDB::SQLToValue($countSQL) !== '1') {
            throw new Exception('The test page does not exist in the `ColbyPages` table.');
        }

        CBModels::deleteModelsByID([$ID]);

        if (CBDB::SQLToValue($countSQL) !== '0') {
            throw new Exception('The test page still exists in the `ColbyPages` table.');
        }
    }
}

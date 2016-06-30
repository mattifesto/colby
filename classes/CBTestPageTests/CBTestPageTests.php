<?php

final class CBTestPageTests {

    /**
     * @return null
     */
    public static function test() {
        $ID = '1406f65b87c45a3927672cf3634a88d6daeca48b';
        $IDAsSQL = CBHex160::toSQL($ID);
        $countSQL = "SELECT COUNT(*) FROM `ColbyPages` WHERE `archiveID` = {$IDAsSQL}";

        Colby::query('START TRANSACTION');

        CBModels::deleteModelsByID([$ID]);

        Colby::query('COMMIT');

        if (CBDB::SQLToValue($countSQL) !== '0') {
            throw new Exception('The test page already exists in the `ColbyPages` table.');
        }

        $spec = CBModels::modelWithClassName('CBTestPage', ['ID' => $ID]);
        $spec->description = 'A test page for page classes';
        $spec->published = time();
        $spec->title = 'Hello, world!';
        $spec->URIPath = 'hello-world';

        Colby::query('START TRANSACTION');

        CBModels::save([$spec]);

        Colby::query('COMMIT');

        if (CBDB::SQLToValue($countSQL) !== '1') {
            throw new Exception('The test page does not exist in the `ColbyPages` table.');
        }

        // Comment out the remaining lines of this function to leave the test
        // page in so that it can be viewed and searched for.

        Colby::query('START TRANSACTION');

        CBModels::deleteModelsByID([$ID]);

        Colby::query('COMMIT');

        if (CBDB::SQLToValue($countSQL) !== '0') {
            throw new Exception('The test page still exists in the `ColbyPages` table.');
        }
    }
}

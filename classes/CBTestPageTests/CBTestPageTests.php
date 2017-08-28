<?php

final class CBTestPageTests {

    /**
     * @return null
     */
    public static function test() {
        $ID = '1406f65b87c45a3927672cf3634a88d6daeca48b';
        $IDAsSQL = CBHex160::toSQL($ID);
        $countSQL = "SELECT COUNT(*) FROM `ColbyPages` WHERE `archiveID` = {$IDAsSQL}";

        CBDB::transaction(function () use ($ID) {
            CBModels::deleteModelsByID([$ID]);
        });

        if (CBDB::SQLToValue($countSQL) !== '0') {
            throw new Exception('The test page already exists in the `ColbyPages` table.');
        }

        $spec = (object)[
            'className' => 'CBTestPage',
            'ID' => $ID,
            'description' => 'A test page for page classes',
            'isPublished' => true,
            'publicationTimeStamp' => time(),
            'title' => 'Hello, world!',
            'URI' => 'hello-world',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save([$spec]);
        });

        if (CBDB::SQLToValue($countSQL) !== '1') {
            throw new Exception('The test page does not exist in the `ColbyPages` table.');
        }

        /* test search text */

        $searchText = CBDB::SQLToValue("SELECT `searchText` FROM `ColbyPages` WHERE `archiveID` = {$IDAsSQL}");

        if (!preg_match('/^Hello, world! A test page for/', $searchText)) {
            throw new Exception("The test doesn't recognize the page search text: {$searchText}");
        }

        /* test render */

        $model = CBModels::fetchModelByID($ID);

        try {
            ob_start();
            CBPage::render($model);
            ob_end_clean();
        } catch (Throwable $throwable) {
            ob_end_clean();
            throw $throwable;
        }

        // Comment out the remaining lines of this function to leave the test
        // page in so that it can be viewed and searched for.

        CBDB::transaction(function () use ($ID) {
            CBModels::deleteModelsByID([$ID]);
        });

        if (CBDB::SQLToValue($countSQL) !== '0') {
            throw new Exception('The test page still exists in the `ColbyPages` table.');
        }
    }
}

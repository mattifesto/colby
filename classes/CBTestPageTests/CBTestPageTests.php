<?php

final class CBTestPageTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'type' => 'server',
            ],
            (object)[
                'name' => 'toggleNonstandardTestPage',
                'type' => 'interactive_server',
                'description' => CBConvert::stringToCleanLine(<<<EOT

                    Toggles the existence of a nonstandard test page.

                EOT),
            ]
        ];
    }



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_general(): stdClass {
        $ID = '1406f65b87c45a3927672cf3634a88d6daeca48b';
        $IDAsSQL = CBID::toSQL($ID);

        $countSQL = <<<EOT

            SELECT  COUNT(*)
            FROM    ColbyPages
            WHERE   archiveID = {$IDAsSQL}

        EOT;

        CBDB::transaction(
            function () use ($ID) {
                CBModels::deleteByID([$ID]);
            }
        );

        if (CBDB::SQLToValue($countSQL) !== '0') {
            throw new Exception(
                'The test page already exists in the `ColbyPages` table.'
            );
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

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save([$spec]);
            }
        );

        if (CBDB::SQLToValue($countSQL) !== '1') {
            throw new Exception(
                'The test page does not exist in the `ColbyPages` table.'
            );
        }

        /* test search text */

        $searchText = CBDB::SQLToValue(
            <<<EOT

                SELECT  searchText
                FROM    ColbyPages
                WHERE   archiveID = {$IDAsSQL}

            EOT
        );

        if (!preg_match('/^Hello, world! A test page for/', $searchText)) {
            throw new Exception(
                "The test doesn't recognize the page search text: {$searchText}"
            );
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

        CBDB::transaction(
            function () use ($ID) {
                CBModels::deleteByID([$ID]);
            }
        );

        if (CBDB::SQLToValue($countSQL) !== '0') {
            throw new Exception(
                'The test page still exists in the `ColbyPages` table.'
            );
        }


        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_general() */



    /**
     * @return object
     */
    static function CBTest_toggleNonstandardTestPage(): stdClass {
        $CBID = 'df3d079d3c8507e1b5d66a97ea4b74ac7ac2b6dc';

        $testPageSpec = CBModels::fetchSpecByIDNullable(
            $CBID
        );

        if ($testPageSpec === null) {
            $testPageSpec = (object)[
                'className' => 'CBTestPage',
                'ID' => $CBID,
                'title' => 'CBTestPageTests Nonstandard Test Page',
            ];

            CBModels::save(
                $testPageSpec
            );

            $message = 'The nonstandard test page was created.';
        } else {
            CBModels::deleteByID(
                $CBID
            );

            $message = 'The nonstandard test page was deleted.';
        }

        return (object)[
            'succeeded' => true,
            'message' => $message,
        ];
    }
    /* CBTest_toggleNonstandardTestPage() */

}

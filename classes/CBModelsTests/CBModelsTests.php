<?php

final class CBModelsTests {

    const testModelIDs = [
        'dbc96d7b92337a3c6b9274c89473ee547ee518d7',
        '728038c2af4e49fb265c82971ac441f96d591056',
        '95397735c2fd6d7e75a2d059fededae693665f50'
    ];



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v478.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBModel',
            'CBModels',
            'CBTest',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'fetchModelByID',
                'type' => 'server',
            ],

            (object)[
                'name' => 'fetchModelsByID',
                'type' => 'server',
            ],

            (object)[
                'name' => 'fetchModelsByID2_maintainPositions',
                'type' => 'server',
            ],

            (object)[
                'name' => 'saveSpecWithoutID',
                'type' => 'server',
            ],

            (object)[
                'name' => 'general',
            ],

            (object)[
                'name' => 'saveAfterSave',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_fetchModelByID(): stdClass {
        Colby::query('START TRANSACTION');
        CBModelsTests::createTestEnvironment();

        /* 1 */

        $ID = CBModelsTests::testModelIDs[2];
        $model = CBModels::fetchModelByID($ID);

        CBModelsTests_TestClass::checkModelWithID($model, $ID, 1);

        /* 2 */

        $ID = CBID::generateRandomCBID();
        $model = CBModels::fetchModelByID($ID);

        if ($model !== false) {
            throw new Exception(
                __METHOD__ .
                ' Calling `CBModel::fetchModelByID` with and ID with ' .
                'no model should return false.'
            );
        }

        Colby::query('ROLLBACK');

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_fetchModelByID() */



    /**
     * @return object
     */
    static function CBTest_fetchModelsByID(): stdClass {
        Colby::query('START TRANSACTION');
        CBModelsTests::createTestEnvironment();

        /* 1 */

        $models = CBModels::fetchModelsByID(CBModelsTests::testModelIDs);

        if (count($models) !== 3) {
            throw new Exception(
                __METHOD__ . ' Test 1: Array should contain 3 models.'
            );
        }

        for ($i = 0; $i < 3; $i++) {
            $ID = CBModelsTests::testModelIDs[$i];
            CBModelsTests_TestClass::checkModelWithID($models[$ID], $ID, 1);
        }

        /* 2 */

        $IDs = [
            CBID::generateRandomCBID(),
            CBID::generateRandomCBID(),
            CBID::generateRandomCBID(),
        ];

        $models = CBModels::fetchModelsByID($IDs);

        if ($models !== []) {
            throw new Exception(
                __METHOD__ . ' Test 2: Array should be empty.'
            );
        }

        /* 3 */

        $IDs = [
            CBID::generateRandomCBID(),
            CBID::generateRandomCBID(),
            CBID::generateRandomCBID(),
        ];

        $IDs = array_merge(CBModelsTests::testModelIDs, $IDs);
        $models = CBModels::fetchModelsByID($IDs);

        if (count($models) !== 3) {
            throw new Exception(
                __METHOD__ . ' Test 3: Array should contain 3 models.'
            );
        }

        Colby::query('ROLLBACK');

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_fetchModelsByID() */



    /**
     * @return object
     */
    static function
    CBTest_fetchModelsByID2_maintainPositions(
    ): stdClass {
        $CBIDs = [];

        do {
            $SQL = <<<EOT

                SELECT
                LOWER(HEX(ID))

                FROM
                CBModels

                ORDER BY
                RAND()

                LIMIT
                10

            EOT;

            $CBIDs = CBDB::SQLToArray(
                $SQL
            );

            $models = CBModels::fetchModelsByID2(
                $CBIDs
            );

            if (
                !CBModelsTests::havePositionsBeenMaintained(
                    $CBIDs,
                    $models
                )
            ) {
                break;
            }
        } while (true);

        $models = CBModels::fetchModelsByID2(
            $CBIDs,
            true /* maintainPositions */
        );

        if (
            !CBModelsTests::havePositionsBeenMaintained(
                $CBIDs,
                $models
            )
        ) {
            return CBTest::valueIssueFailure(
                'position check',
                (object)[
                    'CBIDs' => $CBIDs,
                    'models' => $models
                ],
                'the model positions do not match the CBID positions'
            );
        }


        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_fetchModelsByID2_maintainPositions() */


    /**
     * CBModel::build() is allowed to return a model without an ID, however
     * these models cannot be saved.
     */
    static function CBTest_saveSpecWithoutID(): stdClass {
        $actualSourceCBID = null;
        $expectedSourceCBID = '3754cbe6a23732edfaed0d357946840a1bf66bb6';

        try {
            Colby::query('START TRANSACTION');

            $spec = (object)[
                'className' => 'CBViewPage',
            ];

            CBModels::save([$spec]);

            Colby::query('ROLLBACK');
        } catch (Throwable $throwable) {
            Colby::query('ROLLBACK');

            $actualSourceCBID = CBException::throwableToSourceCBID(
                $throwable
            );
        }

        if ($actualSourceCBID !== $expectedSourceCBID) {
            return CBTest::resultMismatchFailure(
                'sourceCBID',
                $actualSourceCBID,
                $expectedSourceCBID
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_saveSpecWithoutID() */



    /* -- functions  -- -- -- -- -- */



    /**
     * This function assumes that the CBModels functionality is working. The
     * functions that call it will be testing to see if that is actually true.
     *
     * A transaction should be started before this function is called.
     *
     * @return null
     */
    private static function
    createTestEnvironment(
    ) {
        $specs = CBModels::fetchSpecsByID(
            CBModelsTests::testModelIDs,
            [
                'createSpecForIDCallback' => function ($ID) {
                    $spec = (object)[
                        'className' => 'CBModelsTests_TestClass',
                        'ID' => $ID,
                    ];

                    $spec->title = "Title for {$ID}";
                    $spec->name = "Name {$ID}";

                    return $spec;
                }
            ]
        );

        CBModels::save(array_values($specs));
    }
    /* createTestEnvironment() */



    /**
     * @return bool
     */
    private static function
    havePositionsBeenMaintained(
        array $CBIDs,
        array $models
    ): bool {
        $count = count(
            $CBIDs
        );

        if (
            count($models) !== $count
        ) {
            return false;
        }

        for (
            $index = 0;
            $index < $count;
            $index += 1
        ) {
            $CBID = $CBIDs[$index];

            $modelCBID = CBModel::getCBID(
                $models[$index]
            );

            if (
                $CBID !== $modelCBID
            ) {
                return false;
            }
        }

        return true;
    }
    /* havePositionsBeenMaintained() */

}



final class CBModelsTests_TestClass {

    /**
     * Checks a model of this class.
     *
     * @return null
     */
    static function checkModelWithID(stdClass $model, $ID, $version = false) {
        if ($model->ID !== $ID) {
            throw new Exception(
                __METHOD__ . ' Incorrect model ID'
            );
        }

        if ($model->className !== __CLASS__) {
            throw new Exception(
                __METHOD__ . ' Incorrect `className` property'
            );
        }

        /**
         * At one point the "created" property was set on the model when the
         * model was saved. This is no longer happens.
         */
        if (isset($model->created)) {
            throw new Exception(
                'The model should not have its "created" property set.'
            );
        }

        /**
         * At one point the "modified" property was set on the model when the
         * model was saved. This is no longer happens.
         */
        if (isset($model->modified)) {
            throw new Exception(
                'The model should not have its "modified" property set.'
            );
        }

        if ($model->name !== "Name {$ID}") {
            throw new Exception(
                __METHOD__ . ' Incorrect `name` property'
            );
        }

        if ($model->nameAsHTML !== "Name {$ID}") {
            throw new Exception(
                __METHOD__ . ' Incorrect `nameAsHTML` property'
            );
        }

        if ($model->title !== "Title for {$ID}") {
            throw new Exception(
                __METHOD__ . ' The `title` property is not correct.'
            );
        }

        if ($version !== false && $model->version !== $version) {
            $actual = json_encode($model->version);
            $expected = json_encode($version);
            throw new Exception(
                __METHOD__ .
                " Model version: {$actual}, Expected version: {$expected}"
            );
        }
    }
    /* checkModelWithID() */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $model = (object)[
            'name' => CBModel::valueToString($spec, 'name'),
        ];

        $model->nameAsHTML = cbhtml($model->name);

        return $model;
    }
    /* CBModel_build() */

}

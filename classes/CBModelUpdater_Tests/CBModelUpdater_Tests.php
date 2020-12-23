<?php

final class CBModelUpdater_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [

            (object)[
                'name' => 'fetchByCBID',
                'type' => 'server',
            ],

            (object)[
                'name' => 'updateIfExists',
                'type' => 'server',
            ],

        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    CBTest_fetchByCBID(
    ): stdClass {

        /* prepare */

        $CBID = CBTest::getTemporaryModelCBID();

        CBModels::deleteByID(
            $CBID
        );

        $updater = CBModelUpdater::fetchByCBID(
            $CBID
        );

        $spec = ($updater->CBModelUpdater_getSpec)();

        /* test */

        $testName = "model doesn't exist test";

        if ($spec !== null) {
            return CBTest::valueIssueFailure(
                "{$testName}: The spec should be null.",
                $spec
            );
        }

        /* prepare */

        $spec = (object)[];

        CBModel::setCBID(
            $spec,
            $CBID
        );

        CBModel::setClassName(
            $spec,
            'CBViewPage'
        );

        CBViewPage::setPageSettingsClassName(
            $spec,
            'CBPageSettingsForResponsivePages'
        );

        CBModels::save(
            $spec
        );

        $updater = CBModelUpdater::fetchByCBID(
            $CBID
        );

        $spec = ($updater->CBModelUpdater_getSpec)();

        $version = CBModel::getVersion(
            $spec
        );

        /* test */

        $testName = "model exists test";

        if ($version !== 1) {
            return CBTest::valueIssueFailure(
                "{$testName}: The version should be 1.",
                $spec
            );
        }

        /* prepare */

        ($updater->CBModelUpdater_save)();

        $updater = CBModelUpdater::fetchByCBID(
            $CBID
        );

        $spec = ($updater->CBModelUpdater_getSpec)();

        $version = CBModel::getVersion(
            $spec
        );

        /* test */

        $testName = "after updater saved with no changes";

        if ($version !== 1) {
            return CBTest::valueIssueFailure(
                "{$testName}: The version should still be 1.",
                $spec
            );
        }

        /* prepare */

        CBViewPage::setTitle(
            $spec,
            'New Title'
        );

        ($updater->CBModelUpdater_save)();

        $updater = CBModelUpdater::fetchByCBID(
            $CBID
        );

        $spec = ($updater->CBModelUpdater_getSpec)();

        $version = CBModel::getVersion(
            $spec
        );

        /* test */

        $testName = "after updater saved with changes";

        if ($version !== 2) {
            return CBTest::valueIssueFailure(
                "{$testName}: The version should now be 2.",
                $spec
            );
        }

        /* done */

        CBModels::deleteByID(
            $CBID
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_fetchByCBID() */



    /**
     * @return object
     */
    static function
    CBTest_updateIfExists(
    ): stdClass {
        $modelID = 'dc325defccbfaeb390304b35e4073d2a8c19063a';
        $spec = (object)[
            'className' => 'CBMessageView',
            'ID' => $modelID,
        ];


        /* regular update */

        CBModelUpdater::update($spec);

        $actualResult = CBModels::fetchSpecByIDNullable($modelID);
        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'ID' => $modelID,
            'version' => 1,
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure(
                'update',
                $actualResult,
                $expectedResult
            );
        }


        /* delete model */

        CBModels::deleteByID($modelID);


        /* update if exists */

        CBModelUpdater::updateIfExists($spec);

        $actualResult = CBModels::fetchSpecByIDNullable($modelID);
        $expectedResult = null;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'updateIfExists',
                $actualResult,
                $expectedResult
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_updateIfExists() */

}

<?php

final class
CBModelUpdater_Tests {

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
                'name' => 'objectOrientedMultiSave',
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
    fetchByCBID(
    ): stdClass {

        /* prepare */

        $CBID = CBTest::getTemporaryModelCBID();

        CBDB::transaction(
            function () use (
                $CBID
            ) {
                CBModels::deleteByID(
                    $CBID
                );
            }
        );

        $updater = CBModelUpdater::fetchByCBID(
            $CBID
        );

        $spec = ($updater->CBModelUpdater_getSpec)();

        /* test */

        $testName = "model is version 0";

        $version = CBModel::getVersion(
            $spec
        );

        if ($version !== 0) {
            return CBTest::valueIssueFailure(
                "{$testName}: The spec version should be 0.",
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

        CBViewPage::setURI(
            $spec,
            $CBID
        );

        CBDB::transaction(
            function () use (
                $spec
            ) {
                CBModels::save(
                    $spec
                );
            }
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

        CBDB::transaction(
            function () use (
                $updater
            ) {
                ($updater->CBModelUpdater_save)();
            }
        );

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

        CBDB::transaction(
            function () use (
                $CBID
            ) {
                CBModels::deleteByID(
                    $CBID
                );
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* fetchByCBID() */



    /**
     * @return object
     */
    static function
    objectOrientedMultiSave(
    ): stdClass {
        $CBID = '056936c040c40fbad3932cd95fbaa5851f0be730';

        CBDB::transaction(
            function () use (
                $CBID
            ) {
                CBModels::deleteByID(
                    $CBID
                );
            }
        );

        $modelUpdater = new CBModelUpdater(
            $CBID
        );

        CBModel::setClassName(
            $modelUpdater->getSpec(),
            'CBMessageView'
        );

        CBMessageView::setCBMessage(
            $modelUpdater->getSpec(),
            'message 1'
        );

        CBDB::transaction(
            function () use (
                $modelUpdater
            ) {
                $modelUpdater->save2();
            }
        );

        CBMessageView::setCBMessage(
            $modelUpdater->getSpec(),
            'message 2'
        );

        CBDB::transaction(
            function () use (
                $modelUpdater
            ) {
                $modelUpdater->save2();
            }
        );

        CBMessageView::setCBMessage(
            $modelUpdater->getSpec(),
            'message 3'
        );

        CBDB::transaction(
            function () use (
                $modelUpdater
            ) {
                $modelUpdater->save2();
            }
        );

        $messageViewModel = CBModels::fetchModelByCBID(
            $CBID
        );

        $actualCBMessage = CBMessageView::getCBMessage(
            $messageViewModel
        );

        $expectedCBMessage = 'message 3';

        if ($actualCBMessage !== $expectedCBMessage) {
            return CBTest::resultMismatchFailure(
                'updateIfExists',
                $actualCBMessage,
                $expectedCBMessage
            );
        }

        CBDB::transaction(
            function () use (
                $CBID
            ) {
                CBModels::deleteByID(
                    $CBID
                );
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* objectOrientedMultiSave() */



    /**
     * @return object
     */
    static function
    updateIfExists(
    ): stdClass {
        $modelID = 'dc325defccbfaeb390304b35e4073d2a8c19063a';

        $spec = (object)[
            'className' => 'CBMessageView',
            'ID' => $modelID,
        ];


        /* regular update */

        CBModelUpdater::update(
            $spec
        );

        $actualResult = CBModels::fetchSpecByIDNullable(
            $modelID
        );

        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'ID' => $modelID,
            'version' => 1,
        ];

        if (
            $actualResult != $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'update',
                $actualResult,
                $expectedResult
            );
        }


        /* delete model */

        CBDB::transaction(
            function () use (
                $modelID
            ) {
                CBModels::deleteByID(
                    $modelID
                );
            }
        );


        /* update if exists */

        CBModelUpdater::updateIfExists(
            $spec
        );

        $actualResult = CBModels::fetchSpecByIDNullable(
            $modelID
        );

        $expectedResult = null;

        if (
            $actualResult !== $expectedResult
        ) {
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
    /* updateIfExists() */

}

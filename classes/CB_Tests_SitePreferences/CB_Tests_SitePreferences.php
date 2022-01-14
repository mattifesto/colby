<?php

final class
CB_Tests_SitePreferences {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'type' => 'server',
                'name' => 'general',
            ],
            (object)[
                'type' => 'server',
                'name' => 'setFrontPageID',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    general(
    ): stdClass {

        /* administratorEmails */

        $model = CBModel::build(
            (object)[
                'className' => 'CBSitePreferences',
                'administratorEmails' => (
                    '   matt@mattifesto.com ' .
                    ' matt@mattifesto2.com  ,' .
                    ' matt@mattifesto.com ' .
                    ' , , matt@mattifesto3.com ,'
                ),
            ]
        );

        $actualResult = $model->administratorEmails;

        $expectedResult = [
            'matt@mattifesto.com',
            'matt@mattifesto2.com',
            'matt@mattifesto3.com',
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure(
                'administratorEmails',
                $actualResult,
                $expectedResult
            );
        }

        /* CBSitePreferences::debug() */

        $value = CBSitePreferences::debug();

        if (!is_bool($value)) {
            throw new Exception(
                'CBSitePreferences::debug() should return a boolean value.'
            );
        }

        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* general() */



    /**
     * @return object
     */
    static function
    setFrontPageID(
    ): stdClass {
        $testSitePreferencesModelCBID = (
            'e46db04c351e35e5b86be13aa8f5858ddb84ab43'
        );

        /* no page actually exists for this CBID */
        $testFrontPageCBID = '5d7f6ceb893ff208d50e4abf548fec0f8d7feaf0';

        CBDB::transaction(
            function () use (
                $testSitePreferencesModelCBID
            ) {
                CBModels::deleteByID(
                    $testSitePreferencesModelCBID
                );
            }
        );

        $testSitePreferencesSpec = CBModel::createSpec(
            'CBSitePreferences',
            $testSitePreferencesModelCBID
        );

        CBDB::transaction(
            function () use (
                $testSitePreferencesSpec
            ) {
                CBModels::save(
                    $testSitePreferencesSpec
                );
            }
        );

        CBSitePreferences::setFrontPageID(
            $testFrontPageCBID,
            $testSitePreferencesModelCBID
        );

        /* done */

        CBDB::transaction(
            function () use (
                $testSitePreferencesModelCBID
            ) {
                CBModels::deleteByID(
                    $testSitePreferencesModelCBID
                );
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* setFrontPageID() */

}

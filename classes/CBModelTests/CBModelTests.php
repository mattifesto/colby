<?php

final class CBModelTests {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v455.1.js', cbsysurl()),
        ];
    }

    /**
     * @return [[<className>, <testName>]]
     */
    static function CBTest_javaScriptTests(): array {
        return [
            ['CBModel', 'classFunction'],
            ['CBModel', 'equals'],
            ['CBModel', 'value'],
        ];
    }

    /**
     * @return [[<className>, <testName>]]
     */
    static function CBTest_phpTests(): array {
        return [
            ['CBModel', 'upgrade'],
            ['CBModel', 'upgradeSpecWithID'],
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBMessageMarkup',
            'CBModel',
            'CBTest',
        ];
    }

    /**
     * This test runs a CBModel::build() test for all known classes.
     */
    static function buildTest() {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $spec = (object)[
                'className' => $className,
            ];

            $model = CBModel::build($spec);
        }
    }

    /**
     * This test checks the result to CBModel::build() when the converstion
     * function does the minimum amount of work.
     */
    static function buildMinimalImplementationTest() {
        $spec = (object)[
            'className' => 'CBModelTests_TestClass1',
            'ID' => 'c4247a40d9d85524607e6e87cc1d138806765d59',
            'title' => 'Test Title',
        ];

        $model = CBModel::build($spec);
        $expectedModel = (object)[
            'className' => 'CBModelTests_TestClass1',
            'ID' => 'c4247a40d9d85524607e6e87cc1d138806765d59',
            'title' => 'Test Title',
        ];

        if ($model != $expectedModel) {
            throw new Exception('The model differs from the expected model.');
        }
    }

    /**
     * This test runs a CBModel::upgrade() test for all known classes.
     */
    static function toSearchTextTest() {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $spec = (object)[
                'className' => $className,
            ];

            $searchText = CBModel::toSearchText($spec);
        }
    }

    /**
     * This test creates a test spec for all known classes and passes it to
     * CBModel::upgrade().
     *
     * @return object
     */
    static function CBTest_upgrade(): stdClass {
        CBLog::buffer(function () {
            $classNames = CBAdmin::fetchClassNames();

            foreach ($classNames as $className) {
                $spec = (object)[
                    'className' => $className,
                    'title' => 'This is the title of a test model created by CBTest_upgrade() in CBModelTests',
                ];

                $upgradedSpec = CBModel::upgrade($spec);
            }
        });

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * This test verifies that if log entires are made while a spec with an ID
     * is being upgraded, that those entires will have a modelID equal to the
     * spec's ID.
     *
     * This tests has to read the log entry out of the CBLog table because the
     * actual entry made will not have have a modelID specified.
     *
     * @return object
     */
    static function CBTest_upgradeSpecWithID(): stdClass {
        $ID = CBHex160::random();
        $spec = (object)[
            'className' => 'CBModelTests_TestClass1',
            'ID' => $ID,
        ];

        CBModel::upgrade($spec);

        $entries = CBLog::entries((object)[
            'modelID' => $ID,
        ]);

        /* log entry count */

        $actual = count($entries);
        $expected = 1;

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'log entry count',
                $actual,
                $expected
            );
        }

        /* log entry source ID */

        $actual = CBModel::valueAsID($entries[0], 'sourceID');
        $expected = '15f8d83aef490873969223172e5b218a1cb8d987';

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'log entry source ID',
                $actual,
                $expected
            );
        }

        /* log entry model ID */

        $actual = CBModel::valueAsID($entries[0], 'modelID');
        $expected = $ID;

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'log entry model ID',
                $actual,
                $expected
            );
        }

        /* remove log entry from CBLog */

        $IDAsSQL = CBHex160::toSQL($ID);

        Colby::query("DELETE FROM CBLog WHERE modelID = {$IDAsSQL}");

        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
}

/**
 *
 */
final class CBModelTests_TestClass1 {

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec) {
        return (object)[];
    }

    /**
     * @param object $spec
     *
     * @param object
     */
    static function CBModel_upgrade(stdClass $spec): stdClass {
        CBLog::log((object)[
            'message' => 'test log entry',
            'sourceClassName' => __CLASS__,
            'sourceID' => '15f8d83aef490873969223172e5b218a1cb8d987',
        ]);

        return $spec;
    }
}

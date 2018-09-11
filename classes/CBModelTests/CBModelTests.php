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
     * This test runs a CBModel::upgrade() test for all known classes.
     */
    static function upgradeTest() {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $spec = (object)[
                'className' => $className,
            ];

            $upgradedSpec = CBModel::upgrade($spec);
        }
    }
}


final class CBModelTests_TestClass1 {

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec) {
        return (object)[];
    }
}

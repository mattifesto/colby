<?php

final class
CBModelTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.36.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array {
        return [
            [
                'CBModelTests_CBTest_value_testCases',
                CBModelTests::CBTest_value_testCases(),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBMessageMarkup',
            'CBModel',
            'CBTest',
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
                'name' => 'build_minimalImplementation',
                'type' => 'server',
            ],
            (object)[
                'name' => 'toSearchText',
                'type' => 'server',
            ],
            (object)[
                'name' => 'upgradeSpecWithID',
                'type' => 'server',
            ],
            (object)[
                'name' => 'value',
                'type' => 'server',
            ],

            (object)[
                'name' => 'value',
            ],
        ];
    }
    /* CBTest_getTests() */



    /**
     * @return [[<className>, <testName>]]
     */
    static function CBTest_JavaScriptTests(): array {
        return [
            ['CBModel', 'classFunction'],
            ['CBModel', 'equals'],
        ];
    }



    /* -- tests  -- -- -- -- -- */



    /**
     * This test checks for properties on the model that are placed by
     * CBModel::build(), not by the implementation of CBModel_build().
     *
     * @return object
     */
    static function CBTest_build_minimalImplementation(): stdClass {
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
            throw new Exception(
                'The model differs from the expected model.'
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }



    /**
     * @NOTE 2022_01_09
     *
     *      This is a bizarre test. Not all class names are models. Actually
     *      model classes should have their own search text tests. I'm not
     *      entirely sure why this test exists or what it accomplishes.
     *
     * @return object
     */
    static function
    CBTest_toSearchText(
    ): stdClass {
        $classNames = CBAdmin::fetchClassNames();

        foreach (
            $classNames as $className
        ) {
            $spec = (object)[
                'className' => $className,
            ];

            $searchText = CBModel::toSearchText(
                $spec
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_toSearchText() */



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
        $ID = CBID::generateRandomCBID();
        $spec = (object)[
            'className' => 'CBModelTests_TestClass1',
            'ID' => $ID,
        ];

        CBLog::bufferStart();

        CBModel::upgrade($spec);

        $entries = CBLog::bufferContents();

        CBLog::bufferEndClean();


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

        $actual = CBModel::valueAsID(
            $entries[0],
            'modelID'
        );

        $expected = $ID;

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'log entry model ID',
                $actual,
                $expected
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_upgradeSpecWithID() */



    /**
     * @return object
     */
    static function
    CBTest_value(
    ): stdClass {
        $testCases = CBModelTests::CBTest_value_testCases();

        for (
            $index = 0;
            $index < count($testCases);
            $index += 1
        ) {
            $testCase = $testCases[$index];

            $actualResult = CBModel::value(
                $testCase->originalValue,
                $testCase->keyPath,
            );

            if ($actualResult !== $testCase->expectedResult) {
                return CBTest::resultMismatchFailure(
                    "test index {$index}",
                    $actualResult,
                    $testCase->expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_value() */



    /**
     * @return [object]
     */
    private static function
    CBTest_value_testCases(
    ): array {
        return [
            (object)[
                'originalValue' => 1,
                'keyPath' => '',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => 1,
                'keyPath' => '...',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => [
                    (object)[
                        'foo' => 'bar'
                    ]
                ],
                'keyPath' => '...',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => (object)[
                    'foo' => 'bar'
                ],
                'keyPath' => '...',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => [
                    4,
                    5,
                    6
                ],
                'keyPath' => '[1]',
                'expectedResult' => 5,
            ],
            (object)[
                'originalValue' => [
                    4,
                    5,
                    6
                ],
                'keyPath' => '[1a]',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => [
                    4,
                    (object)[
                        'foo' => 'bar',
                    ],
                    6
                ],
                'keyPath' => '[1].foo',
                'expectedResult' => 'bar',
            ],
            (object)[
                'originalValue' => (object)[
                    'foo' => 'bar',
                ],
                'keyPath' => 'foo',
                'expectedResult' => 'bar',
            ],
            (object)[
                'originalValue' => (object)[
                    'foo' => [
                        (object)[
                            'z' => 'x',
                        ],
                        (object)[
                            'bar' => (object)[
                                'baz' => 'hi',
                            ],
                        ],
                    ],
                ],
                'keyPath' => 'foo.[1].bar.baz',
                'expectedResult' => 'hi',
            ],
            (object)[
                /* bug fix test */
                'originalValue' => null,
                'keyPath' => 'color',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => (object)[
                    'color' => 'red'
                ],
                'keyPath' => 'color',
                'expectedResult' => 'red',
            ],
            (object)[
                /* bug fix test */
                'originalValue' => (object)[
                    'color' => null,
                ],
                'keyPath' => 'color.shade',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => (object)[
                    'color' => (object)[
                        'shade' => 'light',
                    ],
                ],
                'keyPath' => 'color.shade',
                'expectedResult' => 'light',
            ],
            (object)[
                'originalValue' => (object)[
                    'number' => 42,
                ],
                'keyPath' => 'number',
                'expectedResult' => 42,
            ],
        ];
    }
    /* CBTest_value_testCases() */

}
/* CBModelTests */



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
        CBLog::log(
            (object)[
                'message' => 'test log entry',
                'sourceClassName' => __CLASS__,
                'sourceID' => '15f8d83aef490873969223172e5b218a1cb8d987',
            ]
        );

        return $spec;
    }
}

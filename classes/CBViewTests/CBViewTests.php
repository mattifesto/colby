<?php

final class CBViewTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'filterSubviews',
                'type' => 'server',
            ],
            (object)[
                'name' => 'getAndSetSubviews',
                'type' => 'server',
            ],
            (object)[
                'name' => 'render',
                'type' => 'server',
            ],
            (object)[
                'name' => 'renderSpec',
                'type' => 'server',
            ],
            (object)[
                'name' => 'toSubviews',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_filterSubviews(): stdClass {
        $originalViewModel = (object)[
            'subviews' => [
                (object)[
                    'action' => 'keep_0_0',
                    'subviews' => [
                        (object)[
                            'action' => 'keep_1_0',
                        ],
                        (object)[
                            'action' => 'remove',
                        ],
                        (object)[
                            'action' => 'keep_1_2',

                        ],
                    ]
                ],
                (object)[
                    'action' => 'remove',
                ],
                (object)[
                    'action' => 'keep_0_2',
                ],
            ],
        ];

        $expectedViewModel = (object)[
            'subviews' => [
                (object)[
                    'action' => 'keep_0_0',
                    'subviews' => [
                        (object)[
                            'action' => 'keep_1_0',
                        ],
                        (object)[
                            'action' => 'keep_1_2',

                        ],
                    ]
                ],
                (object)[
                    'action' => 'keep_0_2',
                ],
            ],
        ];

        $actualViewModel = CBModel::clone($originalViewModel);

        CBView::filterSubviews(
            $actualViewModel,
            function ($viewModel) {
                return CBModel::valueToString($viewModel, 'action') != 'remove';
            }
        );

        if ($actualViewModel != $expectedViewModel) {
            return CBTest::resultMismatchFailure(
                'Test 1',
                $actualViewModel,
                $expectedViewModel
            );
        }

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* CBTest_filterSubviews() */



    /**
     * @return object
     */
    static function CBTest_getAndSetSubviews(): stdClass {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $model = (object)[
                'className' => $className,
            ];

            CBView::setSubviews(
                $model,
                CBViewTests::testSubviewSpecs()
            );

            $actualSubviews = CBView::getSubviews($model);
            $expectedSubviews = CBViewTests::testSubviewSpecs();

            if ($actualSubviews != $expectedSubviews) {
                return CBTest::resultMismatchFailure(
                    'test 1',
                    $actualSubviews,
                    $expectedSubviews
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_getAndSetSubviews() */



    /**
     * @return object
     */
    static function CBTest_render(): stdClass {
        $testName = 'model has invalid class name';
        $actualSourceCBID = 'no exception';
        $expectedSourceCBID = 'd96bf5026de5b05eb1a721da95ea8decf11dcd04';

        try {
            CBView::$testModeIsActive = true;

            $viewModel = (object)[
                'className' => 'CBViewTests_a.b.c'
            ];

            CBView::render($viewModel);
        } catch (Throwable $throwable) {
            $actualSourceCBID = CBException::throwableToSourceCBID(
                $throwable
            );
        } finally {
            CBView::$testModeIsActive = false;
        }

        if ($actualSourceCBID !== $expectedSourceCBID) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualSourceCBID,
                $expectedSourceCBID
            );
        }



        $testName = 'class has not implemented render';
        $actualSourceCBID = 'no exception';
        $expectedSourceCBID = 'c69e407d30d625f060cf0589a4991531f59c6561';

        try {
            CBView::$testModeIsActive = true;

            $viewModel = (object)[
                'className' => 'CBViewTests_renderNotImplemented'
            ];

            CBView::render($viewModel);
        } catch (Throwable $throwable) {
            $actualSourceCBID = CBException::throwableToSourceCBID(
                $throwable
            );
        } finally {
            CBView::$testModeIsActive = false;
        }

        if ($actualSourceCBID !== $expectedSourceCBID) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualSourceCBID,
                $expectedSourceCBID
            );
        }



        $testName = 'success';
        $actualSourceCBID = 'no exception';
        $expectedSourceCBID = 'no exception';

        try {
            CBView::$testModeIsActive = true;

            $viewModel = (object)[
                'className' => 'CBViewTests_workingView'
            ];

            CBView::render($viewModel);
        } catch (Throwable $throwable) {
            $actualSourceCBID = CBException::throwableToSourceCBID(
                $throwable
            );
        } finally {
            CBView::$testModeIsActive = false;
        }

        if ($actualSourceCBID !== $expectedSourceCBID) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualSourceCBID,
                $expectedSourceCBID
            );
        }



        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_render() */



    /**
     * @return object
     */
    static function CBTest_renderSpec(): stdClass {
        $testName = 'class does not exist';
        $actualSourceCBID = 'no exception';
        $expectedSourceCBID = '0f170c152f54ebb97ecd2fb0a27055a096276d37';

        try {
            CBView::$testModeIsActive = true;

            $viewSpec = (object)[
                'className' => 'CBViewTests_classDoesNotExist'
            ];

            CBView::renderSpec($viewSpec);
        } catch (Throwable $throwable) {
            $actualSourceCBID = CBException::throwableToSourceCBID(
                $throwable
            );
        } finally {
            CBView::$testModeIsActive = false;
        }

        if ($actualSourceCBID !== $expectedSourceCBID) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualSourceCBID,
                $expectedSourceCBID
            );
        }



        $testName = 'success';
        $actualSourceCBID = 'no exception';
        $expectedSourceCBID = 'no exception';

        try {
            CBView::$testModeIsActive = true;

            $viewSpec = (object)[
                'className' => 'CBViewTests_workingView'
            ];

            CBView::renderSpec($viewSpec);
        } catch (Throwable $throwable) {
            $actualSourceCBID = CBException::throwableToSourceCBID(
                $throwable
            );
        } finally {
            CBView::$testModeIsActive = false;
        }

        if ($actualSourceCBID !== $expectedSourceCBID) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualSourceCBID,
                $expectedSourceCBID
            );
        }



        /**
         * This test tests two things:
         *
         *      1. Rendering a view spec with no class name when test mode is
         *      NOT active should succeed but produce two log entries.
         *
         *      2. The source CBID of the secong log entry should be
         *      'da871db8b36e6fb4f1ec74f5abaf24f8ccf8aac4'.
         */

        ob_start();

        try {
            $logEntries = CBLog::buffer(
                function () {
                    $viewSpec = (object)[];

                    CBView::renderSpec($viewSpec);
                }
            );

            $actualCountOfLogEntries = count($logEntries);
            $expectedCountOfLogEntries = 2;

            if ($actualCountOfLogEntries !== $expectedCountOfLogEntries) {
                return CBTest::resultMismatchFailure(
                    'no class name: count of log entires',
                    $actualCountOfLogEntries,
                    $expectedCountOfLogEntries
                );
            }

            $logEntry = $logEntries[1];

            $actualLogEntrySourceCBID = CBLogEntry::getSourceCBID(
                $logEntry
            );

            $expectedLogEntrySourceCBID = (
                'da871db8b36e6fb4f1ec74f5abaf24f8ccf8aac4'
            );

            if ($actualLogEntrySourceCBID !== $expectedLogEntrySourceCBID) {
                return CBTest::resultMismatchFailure(
                    'no class name: log entry source CBID',
                    $actualLogEntrySourceCBID,
                    $expectedLogEntrySourceCBID
                );
            }
        } catch (Throwable $error) {
            throw $error;
        } finally {
            ob_end_clean();
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_renderSpec() */



    /**
     * @return object
     */
    static function CBTest_toSubviews(): stdClass {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $model = (object)[
                'className' => $className,
            ];

            $result = CBView::toSubviews($model);
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_toSubviews() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return [model]
     */
    static function testSubviewModels(): array {
        return [
            (object)[
                'className' => 'CBTestView',
                'value' => 42,
            ]
        ];
    }



    /**
     * @return string
     */
    static function testSubviewSearchText(): string {
        return '42 CBTestView';
    }



    /**
     * @return [mixed]
     */
    static function testSubviewSpecs(): array {
        return [
            (object)[
                'className' => 'CBTestView',
                'value' => 42,
            ],
        ];
    }



    /**
     * @return [model]
     */
    static function testSubviewUpgradedSpecs(): array {
        return [
            (object)[
                'className' => 'CBTestView',
                'value' => 42,
            ],
        ];
    }

}
/* CBViewTests */



final class CBViewTests_workingView {

    static function CBModel_build(stdClass $spec): stdClass {
        return (object)[];
    }

    static function CBView_render(stdClass $viewModel): void {
        // no output testing is happening at this time
    }

}

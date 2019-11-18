<?php

final class CBLogTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'bufferEndClean',
                'title' => 'CBLog bufferEndClean',
                'type' => 'server',
            ],
            (object)[
                'name' => 'bufferEndFlush',
                'title' => 'CBLog bufferEndFlush',
                'type' => 'server',
            ],
            (object)[
                'name' => 'noClassName',
                'title' => 'CBLog noClassName',
                'type' => 'server',
            ],
            (object)[
                'name' => 'noMessage',
                'title' => 'CBLog noMessage',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_bufferEndClean(): stdClass {
        CBLog::bufferStart();

        CBLog::log(
            (object)[
                'className' => __CLASS__,
                'message' => 'CBLogTests_firstBuffer',
            ]
        );

        CBLog::bufferStart();

        CBLog::log(
            (object)[
                'className' => __CLASS__,
                'message' => 'CBLogTests_secondBuffer',
            ]
        );

        CBLog::bufferEndClean();

        $buffer = CBLog::bufferContents();

        CBLog::bufferEndClean();

        if (
            count($buffer) !== 1 ||
            $buffer[0]->message !== 'CBLogTests_firstBuffer'
        ) {
            $bufferAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($buffer)
            );

            $message = <<<EOT

                The log entry buffer is not what was expected.

                --- pre\n{$bufferAsMessage}
                ---

EOT;

            return (object)[
                'message' => $message,
                'succeeded' => false,
            ];
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_bufferEndClean() */



    /**
     * @return object
     */
    static function CBTest_bufferEndFlush(): stdClass {
        $sourceID = CBHex160::random();
        $message = <<<EOT

            This is a temporary test log entry created by the function
            CBTest_bufferEndFlush() in the class CBLogTests.

            It should be deleted by the test, so if you see this in the log
            investigate why it wasn't.

        EOT;

        CBLog::bufferStart();

        CBLog::log(
            (object)[
                'message' => $message,
                'sourceClassName' => __CLASS__,
                'sourceID' => $sourceID,
            ]
        );

        CBLog::bufferEndFlush();

        /* log entry count */

        $entries = CBLog::entries(
            (object)[
                'sourceID' => $sourceID,
            ]
        );

        $actual = count($entries);
        $expected = 1;

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'log entry count',
                $actual,
                $expected
            );
        }

        /* delete log entry */

        $sourceIDAsSQL = CBID::toSQL($sourceID);

        Colby::query(
            "DELETE FROM CBLog WHERE sourceID = {$sourceIDAsSQL}"
        );

        /* log entry count after delete */

        $entries = CBLog::entries(
            (object)[
                'sourceID' => $sourceID,
            ]
        );

        $actual = count($entries);
        $expected = 0;

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'log entry count after delete',
                $actual,
                $expected
            );
        }

        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_bufferEndFlush() */



    /**
     * @return object
     */
    static function CBTest_noClassName(): stdClass {
        CBLog::bufferStart();

        $entry = (object)[
            'message' => 'Test',
            'severity' => 7,
        ];

        CBLog::log($entry);

        $buffer = CBLog::bufferContents();

        CBLog::bufferEndClean();

        $bufferSizeFailed = function ($buffer) {
            return count($buffer) !== 2;
        };

        $warningMessageFailed = function ($entry) {
            $message = CBModel::valueToString($entry, 'message');
            return strpos($message, 'CBLog_warning_noClassName') === false;
        };

        $warningSeverityFailed = function ($entry) {
            return CBModel::valueAsInt($entry, 'severity') !== 4;
        };

        if (
            $bufferSizeFailed($buffer) ||
            $warningMessageFailed($buffer[0]) ||
            $warningSeverityFailed($buffer[0])
        ) {
            $bufferAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($buffer)
            );

            $message = <<<EOT

                The log entry buffer is not what was expected.

                --- pre\n{$bufferAsMessage}
                ---

            EOT;

            return (object)[
                'message' => $message,
                'succeeded' => false,
            ];
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_noClassName() */



    /**
     * @return object
     */
    static function CBTest_noMessage(): stdClass {
        CBLog::bufferStart();

        $entry = (object)[
            'className' => __CLASS__,
            'severity' => 7,
        ];

        CBLog::log($entry);

        $buffer = CBLog::bufferContents();

        CBLog::bufferEndClean();

        $bufferSizeFailed = function ($buffer) {
            return count($buffer) !== 2;
        };

        $warningMessageFailed = function ($entry) {
            $message = CBModel::valueToString($entry, 'message');
            return strpos($message, 'CBLog_warning_noMessage') === false;
        };

        $warningSeverityFailed = function ($entry) {
            return CBModel::valueAsInt($entry, 'severity') !== 4;
        };

        if (
            $bufferSizeFailed($buffer) ||
            $warningMessageFailed($buffer[0]) ||
            $warningSeverityFailed($buffer[0])
        ) {
            $bufferAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($buffer)
            );

            $message = <<<EOT

                The log entry buffer is not what was expected.

                --- pre\n{$bufferAsMessage}
                ---

            EOT;

            return (object)[
                'message' => $message,
                'succeeded' => false,
            ];
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_noMessage() */

}

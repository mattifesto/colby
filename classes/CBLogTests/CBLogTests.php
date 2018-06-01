<?php

final class CBLogTests {

    /**
     * @return object
     */
    static function CBTest_bufferEndClean(): stdClass {
        CBLog::bufferStart();

        CBLog::log((object)[
            'className' => __CLASS__,
            'message' => 'CBLogTests_firstBuffer',
        ]);

        CBLog::bufferStart();

        CBLog::log((object)[
            'className' => __CLASS__,
            'message' => 'CBLogTests_secondBuffer',
        ]);

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

    /**
     * This is a code coverage test for bufferEndFlush(). This test does not
     * confirm that the function ran correcty.
     *
     * @return object
     */
    static function CBTest_bufferEndFlush(): stdClass {
        CBLog::bufferStart();

        CBLog::log((object)[
            'className' => __CLASS__,
            'message' => __FUNCTION__ . '() in ' . __CLASS__,
        ]);

        CBLog::bufferEndFlush();

        return (object)[
            'succeeded' => true,
        ];
    }

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

    /**
     * @return [[<class>, <test>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBLog', 'bufferEndClean'],
            ['CBLog', 'bufferEndFlush'],
            ['CBLog', 'noClassName'],
            ['CBLog', 'noMessage'],
        ];
    }
}

<?php

final class CBTextViewTests {

    /**
     * @return [[<class>, <function>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBTextView', 'upgrade'],
        ];
    }

    /**
     * @return object
     */
    static function CBTest_upgrade(): stdClass {
        $text = <<<EOT

            This is the (first) paragraph.

            This is the second paragraph - that talks about {The Wind
            and the Willows}.

EOT;

        $original = (object)[
            'className' => 'CBTextView',
            'text' => $text,
        ];

        $expected = (object)[
            'className' => 'CBMessageView',
            'markup' => CBMessageMarkup::stringToMarkup($text),
        ];

        CBLog::bufferStart();

        $result = CBModel::upgrade($original);

        $buffer = CBLog::bufferContents();

        CBLog::bufferEndClean();

        $bufferIsValid = function ($buffer): bool {
            if (count($buffer) !== 1) {
                return false;
            }

            $sourceID = CBModel::valueAsID($buffer[0], 'sourceID');

            if ($sourceID !== '00754cfbd72404dc44861189397751acabdc4ce7') {
                return false;
            }

            return true;
        };

        if (!$bufferIsValid($buffer)) {
            $bufferAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($buffer)
            );

            $message = <<<EOT

                The log entry buffer is not what was expected:

                --- pre\n{$bufferAsMessage}
                ---

EOT;

            return (object)[
                'message' => $message,
            ];
        }

        if ($result != $expected) {
            $message = CBConvertTests::resultAndExpectedToMessage($result, $expected);

            return (object)[
                'message' => <<<EOT

                    The result upgraded spec does not match the expected upgrade spec:

                    {$message}

EOT
            ];
        }

        return (object)[
            'succeeded' => true,
        ];
    }
}

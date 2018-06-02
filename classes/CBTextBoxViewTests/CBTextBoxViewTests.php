<?php

final class CBTextBoxViewTests {

    /**
     * @return [[<class>, <function>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBTextBoxView', 'upgrade'],
        ];
    }

    /**
     * @return object
     */
    static function CBTest_upgrade(): stdClass {
        $original = (object)[
            'className' => 'CBTextBoxView',
            'titleAsMarkaround' => 'This is the Title',
            'contentAsMarkaround' => <<<EOT

This is the (first) paragraph.

This is the second paragraph - that talks about {The Wind
and the Willows}.

EOT
        ];

        $expected = (object)[
            'className' => 'CBMessageView',
            'markup' => <<<EOT

--- h1
This is the Title
---


This is the \\(first\\) paragraph.

This is the second paragraph \\- that talks about {The Wind
and the Willows}.


EOT
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

            if ($sourceID !== 'f25ecc1f2d21853500bf58b652d9a256db8be0d2') {
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

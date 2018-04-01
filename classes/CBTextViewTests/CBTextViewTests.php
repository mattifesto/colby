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
     * @return ?object
     */
    static function CBTest_upgrade(): ?stdClass {
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

        $result = CBModel::upgrade($original);

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

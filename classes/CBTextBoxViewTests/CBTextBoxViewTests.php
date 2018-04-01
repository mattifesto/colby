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

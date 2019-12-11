<?php

final class CBTextBoxViewTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'upgrade',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



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


        /* test 1 */

        if (!$bufferIsValid($buffer)) {
            return CBTest::valueIssueFailure(
                'test 1',
                $buffer,
                'The log entry buffer is not what was expected.'
            );
        }


        /* test 2 */

        if ($result != $expected) {
            return CBTest::resultMismatchFailure(
                'test 2',
                $result,
                $expected
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_upgrade() */

}

<?php

final class CBHTMLOutput_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'type' => 'server',
                'title' => 'CBHTMLOutput::begin() while active',
                'name' => 'beginWhileActive',
            ],
            (object)[
                'type' => 'server',
                'title' => 'CBHTMLOutput::begin() and CBHTMLOutput::reset()',
                'name' => 'beginAndReset',
            ],
        ];
    }
    /* CBTest_getTests() */


    /* -- tests -- -- -- -- -- */

    /**
     * The series of operations shown here should produce no errors.
     *
     * @return object
     */
    static function CBTest_beginAndReset(): stdClass {
        CBHTMLOutput::reset();
        CBHTMLOutput::reset();
        CBHTMLOutput::reset();

        CBHTMLOutput::begin();
        CBHTMLOutput::reset();

        CBHTMLOutput::begin();
        CBHTMLOutput::reset();
        CBHTMLOutput::reset();

        CBHTMLOutput::begin();
        CBHTMLOutput::reset();
        CBHTMLOutput::reset();
        CBHTMLOutput::reset();

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_beginAndReset() */


    /**
     * @return object
     */
    static function CBTest_beginWhileActive(): stdClass {
        $actualSourceID = null;
        $expectedSourceID = '29ade9f6d4a763a69f69c79a191893743ef3fcef';

        try {
            CBHTMLOutput::begin();
            CBHTMLOutput::begin();
        } catch (Throwable $throwable) {
            CBHTMLOutput::reset();

            $actualSourceID = CBException::throwableToSourceID($throwable);
        }

        if ($actualSourceID !== $expectedSourceID) {
            return CBTest::resultMismatchFailure(
                'Test 1',
                $actualSourceID,
                $expectedSourceID
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_beginWhileActive() */
}

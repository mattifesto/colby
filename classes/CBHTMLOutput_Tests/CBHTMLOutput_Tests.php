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
        ];
    }
    /* CBTest_getTests() */


    /* -- tests -- -- -- -- -- */

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

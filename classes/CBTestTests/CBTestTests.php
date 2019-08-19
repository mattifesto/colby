<?php

final class CBTestTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v512.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'title' => 'CBTest Synchronous Test Sample',
                'name' => 'sample',
            ],
            (object)[
                'title' => 'CBTest Asychronous Test Sample',
                'name' => 'asynchronousSample',
            ],
            (object)[
                'type' => 'server',
                'title' => 'CBTest::getTests_classNameToTests()',
                'name' => 'getTests_classNameToTests',
            ],
            (object)[
                'type' => 'server',
                'title' => 'CBTest::resultMismatchFailureDiff()',
                'name' => 'resultMismatchFailureDiff',
            ],
        ];
    }
    /* CBTest_getTests() */


    /* -- tests -- -- -- -- -- */

    /**
     * @return object
     */
    static function CBTest_getTests_classNameToTests(): stdClass {

        /* wrong return type */

        $actualSourceID = 'no source ID';
        $expectedSourceID = '2f124f63ff0a25662415c894d2eb9f742a74f5c3';

        try {
            CBTest::getTests_classNameToTests(
                'CBTestTests_WrongReturnType'
            );
        } catch (Throwable $throwable) {
            if ($throwable instanceof CBException) {
                $actualSourceID = $throwable->getSourceID();
            }

            if ($actualSourceID !== $expectedSourceID) {
                throw $throwable;
            }
        }


        /* bad test name */

        $actualSourceID = 'no source ID';
        $expectedSourceID = 'a955214c24c7cb1edbb1dfae513220fb63382f1a';

        try {
            CBTest::getTests_classNameToTests(
                'CBTestTests_BadTestName'
            );
        } catch (Throwable $throwable) {
            if ($throwable instanceof CBException) {
                $actualSourceID = $throwable->getSourceID();
            }

            if ($actualSourceID !== $expectedSourceID) {
                throw $throwable;
            }
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_getTests_classNameToTests() */


    /**
     * @return object
     */
    static function CBTest_resultMismatchFailureDiff(): stdClass {
        /* subtest 1 */
        $actualTest = "fred\nbob";
        $expectedTest = "fred";

        $object = CBTest::resultMismatchFailureDiff('subtest 1', $actualTest, $expectedTest);
        $actualSourceID = CBModel::valueToString($object, 'sourceID');
        $expectedSourceID = '08437677d6da8acc68b06c9a4418f7fbd4ffeb76';

        if ($actualSourceID != $expectedSourceID) {
            return CBTest::resultMismatchFailure('subtest 1', $actualSourceID, $expectedSourceID);
        }

        /* subtest 2 */

        $actualTest = "bob";
        $expectedTest = "fred";

        $object = CBTest::resultMismatchFailureDiff('subtest 2', $actualTest, $expectedTest);
        $actualSourceID = CBModel::valueToString($object, 'sourceID');
        $expectedSourceID = '696cd3366b14ad7ac12fd4604a9c8fb41824cb96';

        if ($actualSourceID != $expectedSourceID) {
            return CBTest::resultMismatchFailure('subtest 2', $actualSourceID, $expectedSourceID);
        }

        /* subtest 3 */

        $actualTest = "bob";
        $expectedTest = "bob\nfred";

        $object = CBTest::resultMismatchFailureDiff('subtest 3', $actualTest, $expectedTest);
        $actualSourceID = CBModel::valueToString($object, 'sourceID');
        $expectedSourceID = '2c8897739fcfa9df391fc698894e4a7af566a9a6';

        if ($actualSourceID != $expectedSourceID) {
            return CBTest::resultMismatchFailure('subtest 3', $actualSourceID, $expectedSourceID);
        }

        /* subtest 4 */

        $actualTest = "bob\nfred\nsam";
        $expectedTest = "bob\nfred\nsam";

        $object = CBTest::resultMismatchFailureDiff('subtest 4', $actualTest, $expectedTest);
        $actualSourceID = CBModel::valueToString($object, 'sourceID');
        $expectedSourceID = '3098da7e4559278488a42ad81fbaef5fe0a7575e';

        if ($actualSourceID != $expectedSourceID) {
            return CBTest::resultMismatchFailure('subtest 4', $actualSourceID, $expectedSourceID);
        }

        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_resultMismatchFailureDiff() */
}
/* CBTestTests */


final class CBTestTests_WrongReturnType {
    static function CBTest_getTests() {
        return "bad";
    }
}


final class CBTestTests_BadTestName {
    static function CBTest_getTests() {
        return [

            (object)[
            ],

            2,

            (object)[
            ],

        ];
    }
}

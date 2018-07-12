<?php

final class CBTestTests {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v437.js', cbsysurl())];
    }

    /**
     * @return [[<className>, <testName>]]
     */
    static function CBTest_javaScriptTests(): array {
        return [
            ['CBTest', 'sample'],
            ['CBTest', 'asynchronousSample'],
        ];
    }

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

    /**
     * @return [[<class>, <test>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBTest', 'resultMismatchFailureDiff'],
        ];
    }
}

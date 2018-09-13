<?php

class CBUnitTests {

    /**
     * @param string $pageStub
     *
     * @return [string]
     */
    static function CBAdmin_menuNamePath(string $pageStub) {
        switch ($pageStub) {
            case 'AdminPageException':
                return ['develop', 'test'];

            default:
                return ['develop'];
        }
    }

    /**
     * @param string $pageStub
     *
     * @return void
     */
    static function CBAdmin_render(string $pageStub): void {
        switch ($pageStub) {
            case 'AdminPageException':
                throw new RuntimeException('Admin Page Exception Test');
                break;

            default:
                break;
        }
    }

    /**
     * @return null
     */
    static function CBAjax_errorTest() {
        //throw new RuntimeException(str_repeat("This is a test of a long message. ", 1000));
        throw new RuntimeException('Test exception thrown inside the PHP implementation of CBUnitTests::CBAjax_errorTest()');
    }

    /**
     * @return string
     */
    static function CBAjax_errorTest_group() {
        return 'Developers';
    }

    /**
     * @return null
     */
    static function getListOfTestsForAjax() {
        $response = new CBAjaxResponse();
        $response->tests = CBTest::phpTests();
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function getListOfTestsForAjaxPermissions() {
        return (object)['group' => 'Developers'];
    }
}

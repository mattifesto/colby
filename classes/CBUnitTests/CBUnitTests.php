<?php

class CBUnitTests {

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

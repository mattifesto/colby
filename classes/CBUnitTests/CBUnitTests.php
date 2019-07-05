<?php

class CBUnitTests {

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

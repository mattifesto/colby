--- h1
PHP Test
---

To create a PHP test for the class name my class take the following steps:

--- ol
    --- li
        Create the PHP file (classes/MyClassTests/MyClassTests.php (code)) declaring the
        class (MyClassTests (code)).

        --- pre
final class MyClassTests {

}
        ---
    ---

    --- li
        Add a tests function to the (MyClassTests (code)) class.

        The test function name should start with (CBTest_ (code)).

        This function should return an object.

        The object should have a boolean property named (succeeded (code)) set
        to true if the test passed and false otherwise.

        If the test did not pass the object should have a string property named
        (message (code)) set to message markup that explains why the test
        failed.

        --- pre
/**
 * @return object
 */
static function CBTest_myTest(): stdClass {

    /* test code */

    return (object)[
        'succeeded' => false,
        'message' => 'X was supposed to equal Y',
    ];
}
        ---
    ---

    --- li
        Add the function (CBTest_phpTests\(\) (code)) to the class (MyClass
        (code)) if it does not yet exist.

        --- pre
static function CBTest_phpTests(): array {
    return [
        [<class>, <test>],
        [<class>, <test>],
        [<class>, <test>],
    ];
}
        ---
    ---

    --- li
        Add a new item to the array returned by (CBTest_phpTests\(\) (code)).

        --- pre
['CBMyClass', 'myTest'],
        ---
    ---
---

--- h1
JavaScript Test
---

To create a JavaScript test for the a class named MyClass take the following steps:

--- ol
    --- li
        Create the JavaScript file (classes/MyClassTests/MyClassTests.js (code))
        declaring the global object (MyClassTests (code)).

        --- pre
"use strict";

var MyClassTests = {

};
        ---
    ---

    --- li
        Add a test function to the (MyClassTests (code)) object.

        The test function name should start with (CBTest_ (code)).

        This function will usually return an object, but for asynchronous tests
        it can return a Promise that will resolve to an object.

        The object should have a boolean property named (succeeded (code)) set
        to true if the test passed and false otherwise.

        If the test did not pass the object should have a string property named
        (message (code)) set to message markup that explains why the test
        failed.

        --- pre
/**
 * @return Promise -> object
 */
CBTest_myTest: function () {
    return new Promise(function (resolve, reject) {
        setTimeout(
            function () {
                resolve({
                    succeeded: false,
                    message: 'X was supposed to equal Y',
                });
            },
            1000
        );
    });
}
        ---
    ---

    --- li
        Add the function (CBTest_javaScriptTests\(\) (code)) to the class
        (MyClass (code)) if it does not yet exist.

        --- pre
static function CBTest_javaScriptTests(): array {
    return [
        [<class>, <test>],
        [<class>, <test>],
        [<class>, <test>],
    ];
}
        ---
    ---

    --- li
        Add a new item to the array returned by (CBTest_javaScriptTests\(\)
        (code)).

        --- pre
['CBMyClass', 'myTest'],
        ---
    ---
---

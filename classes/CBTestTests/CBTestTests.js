"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTestTests */

var CBTestTests = {

    /**
     * @return Promise -> object
     */
    CBTest_asynchronousSample: function () {
        let promise = new Promise(
            function (resolve, reject) {

                /**
                 * placeholder: test code
                 */

                setTimeout(
                    function () {
                        CBTest_asynchronousSample_finish();
                    },
                    100
                );

                return;


                /* -- closures -- -- -- -- -- */

                /**
                 * @return undefined
                 */
                function CBTest_asynchronousSample_finish() {

                    /**
                     * placeholder: test code
                     */

                    resolve(
                        {
                            succeeded: true,
                            message: "The asynchronous sample test succeeded.",
                        }
                    );
                }
                /* CBTest_asynchronousSample_finish */
            }
        );
        /* new Promise() */

        return promise;
    },
    /* CBTest_asynchronousSample() */


    /**
     * @return object
     */
    CBTest_sample: function () {

        /**
         * placeholder: run tests to determine if the test has succeeded.
         */

        return {
            succeeded: true,
            message: "The sample test succeeded.",
        };
    },
    /* CBTest_sample() */
};
/* CBTestTests */

"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTestTests */

var CBTestTests = {

    /**
     * @return object|Promise
     */
    CBTest_asynchronousSample: function () {
        return new Promise(function (resolve, reject) {

            /**
             * placeholder: test code
             */

            setTimeout(finish, 100);

            function finish() {

                /**
                 * placeholder: test code
                 */

                resolve({
                    succeeded: true,
                    message: "The asynchronous sample test succeeded.",
                });
            }
        });
    },

    /**
     * @return object|Promise
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
};

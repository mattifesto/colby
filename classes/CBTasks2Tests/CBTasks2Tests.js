"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTasks2Tests */

var CBTasks2Tests = {

    /**
     * @return Promise -> object
     */
    CBTest_stress: function() {
        return new Promise(function (resolve, reject) {
            setTimeout(
                function () {
                    resolve({
                        succeeded: true,
                    });
                },
                1000
            );
        });
    },
};

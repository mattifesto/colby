"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBAdmin_RenderExceptionTest */

var CBAdmin_RenderExceptionTest = {

    /* -- tests -- -- -- -- -- */

    /**
     * @return object
     */
    CBTest_exception: function () {
        window.open('/admin/?c=CBAdmin_RenderExceptionTest');

        return {
            succeeded: true,
        };
    },
    /* CBTest_exception() */
};
/* CBAdmin_RenderExceptionTest */

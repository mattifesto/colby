"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBAdminPageForPagesFind_Tests */
/* global
    CBPageList,
*/


var CBAdminPageForPagesFind_Tests = {

    /* -- tests -- -- -- -- -- */

    /**
     * @return object
     */
    CBTest_CBPageList_createElement: function () {
        CBPageList.createElement(
            [
                {
                    keyValueData: {
                        image: 5,
                    }
                },
            ]
        );

        return {
            succeeded: true,
        };
    },
    /* CBTest_CBPageList_createElement() */
};
/* CBAdminPageForPagesFind_Tests */

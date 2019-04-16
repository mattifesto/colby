"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBEventTests */
/* global
    CBEvent,
    CBTest,
*/

var CBEventTests = {

    /**
     * This test makes sure that when dispatch() is called on an event while
     * dispatch of the event is currently happening, the call to dispatch() will
     * be ignored to avoid an infinite loop.
     *
     * @return object|Promise
     */
    CBTest_recursive: function () {
        let event = CBEvent.create();

        event.addListener(
            function () {
                return undefined;
            }
        );

        event.addListener(
            function () {
                event.dispatch();

                return undefined;
            }
        );

        event.dispatch();
        event.dispatch();

        return {
            succeeded: true,
        };
    },

    /**
     * @return object|Promise
     */
    CBTest_removeListener: function () {
        let count = 0;

        /* prepare */

        let event = CBEvent.create();

        let listener = function () {
            count += 1;
        };

        event.addListener(listener);
        event.addListener(listener);
        event.dispatch();

        /* test */

        {
            let actualResult = count;
            let expectedResult = 1;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "Count test 1",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* prepare */

        event.removeListener(listener);
        event.dispatch();

        /* test */

        {
            let actualResult = count;
            let expectedResult = 1;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "Count test 2",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* done */

        return {
            succeeded: true,
        };
    },
};

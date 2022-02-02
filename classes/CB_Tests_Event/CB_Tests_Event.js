/* global
    CB_Event,
    CBTest,
*/


(function () {
    "use strict";

    window.CB_Tests_Event = {
        CBTest_recursive,
        CBTest_removeListener,
    };



    /**
     * This test makes sure that when dispatch() is called on an event while
     * dispatch of the event is currently happening, the call to dispatch() will
     * be ignored to avoid an infinite loop.
     *
     * @return object|Promise
     */
    function
    CBTest_recursive(
    ) {
        let event = CB_Event.create();

        event.CB_Event_addListener(
            function () {
                return undefined;
            }
        );

        event.CB_Event_addListener(
            function () {
                event.CB_Event_dispatch();

                return undefined;
            }
        );

        event.CB_Event_dispatch();
        event.CB_Event_dispatch();

        return {
            succeeded: true,
        };
    }
    /* CBTest_recursive() */



    /**
     * @return object|Promise
     */
    function
    CBTest_removeListener(
    ) {
        let count = 0;

        /* prepare */

        let event = CB_Event.create();

        let listener = function () {
            count += 1;
        };

        event.CB_Event_addListener(
            listener
        );

        event.CB_Event_addListener(
            listener
        );

        event.CB_Event_dispatch();

        /* test */

        {
            let actualResult = count;
            let expectedResult = 1;

            if (
                actualResult !== expectedResult
            ) {
                return CBTest.resultMismatchFailure(
                    "Count test 1",
                    actualResult,
                    expectedResult
                );
            }
        }

        /* prepare */

        event.CB_Event_removeListener(
            listener
        );

        event.CB_Event_dispatch();

        /* test */

        {
            let actualResult = count;
            let expectedResult = 1;

            if (
                actualResult !== expectedResult
            ) {
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
    }
    /* CBTest_removeListener() */

})();

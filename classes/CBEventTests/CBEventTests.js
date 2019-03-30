"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBEventTests */
/* global
    CBEvent,
*/

var CBEventTests = {

    /**
     * This test makes sure that when dispatch() is called on an event while
     * dispatch of the event is currently happening, the call to dispatch() will
     * be ignored to avoid an infinite loop.
     *
     * @return object | Promise
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
};

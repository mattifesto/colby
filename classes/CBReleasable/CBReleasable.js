"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBReleasable */

var CBReleasable = {

    /**
     * @param object targetObject
     * @param function releaseCallback
     *
     * @return undefined
     */
    activate: function (targetObject, releaseCallback) {
        if (targetObject.CBReleasable !== undefined) {
            throw new Error(
                "The targetObject parameter to CBReleasable.activate() is " +
                "already releasable."
            );
        }

        if (typeof releaseCallback !== "function") {
            throw new Error(
                "The releaseCallback parameter to CBReleasable.activate() " +
                "is not a function."
            );
        }

        let pod = {
            release: release,
        };

        Object.defineProperty(
            targetObject,
            "CBReleasable",
            {
                configurable: true,
                enumerable: false,
                value: pod,
                writable: false,
            }
        );

        return;

        /* -- closures -- -- -- -- -- */

        function release() {
            delete targetObject.CBReleasable;

            releaseCallback();
        }
    },

    /**
     * @return undefined
     */
    assertObjectIsReleasable: function (targetObject) {
        if (typeof targetObject !== "object") {
            throw new Error(
                "The parameter to CBReleasable.assertObjectIsReleasable() " +
                "is not an object."
            );
        }

        if (typeof targetObject.CBReleasable !== "object") {
            throw new Error(
                "The object passed to " +
                "CBReleasable.assertObjectIsReleasable() does not have a " +
                "CBReleasable pod."
            );
        }

        if (typeof targetObject.CBReleasable.release !== "function") {
            throw new Error(
                "The object passed to " +
                "CBReleasable.assertObjectIsReleasable() does not have a " +
                "release function on its CBReleasable pod."
            );
        }
    },
};

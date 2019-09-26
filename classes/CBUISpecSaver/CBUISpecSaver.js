"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModel */
/* globals
    CBUIPanel,
    Colby,
*/

var CBUISpecSaver = {

    specDataByID: {},

    /**
     * @param object args.spec
     * @param function? args.fulfilledCallback
     * @param function? args.rejectedCallback
     *
     * @return object
     *
     *      {
     *          specChangedCallback: function,
     *      }
     */
    create: function (args) {
        if (CBUISpecSaver.specDataByID[args.spec.ID]) {
            CBUIPanel.displayText(
                "This spec already has a specSaver."
            );
        } else {
            CBUISpecSaver.specDataByID[args.spec.ID] = {
                fulfilledCallback: args.fulfilledCallback,
                rejectedCallback: args.rejectedCallback,
                spec: args.spec,
            };
        }

        var specChangedCallback = CBUISpecSaver.specDidChange.bind(
            undefined,
            {
                ID: args.spec.ID,
            }
        );

        return {
            specChangedCallback: specChangedCallback,
        };
    },
    /* create() */


    /**
     * @return Promise
     */
    flush: function () {
        var promises = [];

        if (CBUISpecSaver.flushPromise) {
            return CBUISpecSaver.flushPromise;
        }

        Object.keys(
            CBUISpecSaver.specDataByID
        ).forEach(
            function (ID) {
                var specData = CBUISpecSaver.specDataByID[ID];

                if (specData.timeoutID) {
                    clearTimeout(specData.timeoutID);

                    specData.timeoutID = undefined;

                    CBUISpecSaver.requestSave(
                        {
                            ID: ID
                        }
                    );
                }

                if (specData.promise) {
                    promises.push(specData.promise);
                }
            }
        );

        CBUISpecSaver.flushPromise = Promise.all(
            promises
        ).catch(
            function (error) {
                Colby.reportError(error);
            }
        ).then(
            function () {
                CBUISpecSaver.flushPromise = undefined;
            }
        );

        return CBUISpecSaver.flushPromise;
    },
    /* flush() */


    /**
     * @param hex160 args.ID
     *
     * @return undefined
     */
    specDidChange: function (args) {
        var specData = CBUISpecSaver.specDataByID[args.ID];

        if (specData.timeoutID) {
            clearTimeout(specData.timeoutID);
            specData.timeoutID = undefined;
        }

        specData.timeoutID = setTimeout(callback, 1000);

        function callback() {
            specData.timeoutID = undefined;
            CBUISpecSaver.requestSave({ID: args.ID});
        }
    },
    /* specDidChange() */


    /**
     * This function can be called safely at any time, multiple times, without
     * negative repercussions. However, calling this function directly will
     * force a save even if there have been no changes to the spec.
     *
     * @param hex160 args.ID
     *
     * @return undefined
     */
    requestSave: function (args) {
        var specData = CBUISpecSaver.specDataByID[args.ID];

        // If a timeoutID is set it means this function has been called manually
        // and we can clear the timeout.
        if (specData.timeoutID) {
            clearTimeout(specData.timeoutID);
            specData.timeoutID = undefined;
        }

        // If there is an active request but no pending request then add another
        // request only if the active request is resolved. If there is not an
        // active request, make a request right now.
        if (specData.promise && !specData.hasPendingRequest){
            specData.promise = specData.promise.then(request);
            specData.hasPendingRequest = true;
        } else {
            specData.promise = request();
        }

        return;


        /* -- closures -- -- -- -- -- */

        /**
         * @return Promise
         */
        function request() {
            return Colby.callAjaxFunction(
                "CBModels",
                "save",
                {
                    spec: specData.spec,
                }
            ).then(
                fulfilled,
                rejected
            ).then(
                specData.fulfilledCallback,
                specData.rejectedCallback
            );
        }
        /* request() */


        // When a request is resolved:
        //      1. Update the spec version.
        //      2. If there is a pending request that request will be activated
        //         shortly so remove the pending request flag.
        //      3. If there is no pending request, clear the promise.
        function fulfilled(ajaxResponse) {
            var spec = specData.spec;

            if (spec.version === undefined) {
                spec.version = 1;
            } else {
                spec.version += 1;
            }

            if (specData.hasPendingRequest) {
                specData.hasPendingRequest = undefined;
            } else {
                specData.promise = undefined;
            }

            return ajaxResponse;
        }
        /* fulfilled() */


        // When a request is rejected stop all active and pending requests:
        //      1. Reset specData values.
        //      2. Return a rejected promise to avoid executing further resolved
        //         handlers in the promise chain.
        //
        // TODO: Display an error message? Try again?
        function rejected(error) {
            specData.hasPendingRequest = undefined;
            specData.promise = undefined;

            return Promise.reject(error);
        }
        /* rejected() */
    },
    /* requestSave() */
};

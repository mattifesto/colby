"use strict"; /* jshint strict: global */
/* globals
    Colby,
    Promise */

var CBUISpecSaver = {

    specDataByID: {},

    /**
     * @param object args.spec
     *
     * @return {
     *      specChangedCallback: function,
     * }
     */
    create: function (args) {
        if (CBUISpecSaver.specDataByID[args.spec.ID]) {
            Colby.alert("This spec already has a specSaver.");
        } else {
            CBUISpecSaver.specDataByID[args.spec.ID] = {
                spec: args.spec,
            };
        }

        var specChangedCallback = CBUISpecSaver.specDidChange.bind(undefined, {
            ID: args.spec.ID,
        });

        return {
            specChangedCallback: specChangedCallback,
        };
    },

    /**
     * @return Promise
     */
    flush: function (args) {
        var promises = [];

        if (CBUISpecSaver.flushPromise) {
            return CBUISpecSaver.flushPromise;
        }

        /*
        return Promise.reject(new Error("no way")).catch(Colby.report)
            .then(function () { alert("fulfilled"); })
            .catch(function (error) { alert(error.message); });
        */

        Object.keys(CBUISpecSaver.specDataByID).forEach(function (ID) {
            var specData = CBUISpecSaver.specDataByID[ID];

            if (specData.timeoutID) {
                clearTimeout(specData.timeoutID);
                specData.timeoutID = undefined;
                CBUISpecSaver.requestSave({ID: ID});
            }

            if (specData.promise) {
                promises.push(specData.promise);
            }
        });

        CBUISpecSaver.flushPromise = Promise.all(promises).then(args.callback).catch(Colby.report).then(clear, clear);

        function clear() {
            CBUISpecSaver.flushPromise = undefined;
        }
    },

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

        specData.timeoutID = setTimeout(callback, 5000);

        function callback() {
            specData.timeoutID = undefined;
            CBUISpecSaver.requestSave({ID: args.ID});
        }
    },

    /**
     * This function can be called safely at any time, multiple times, without
     * negative repercussions. However, calling this function directly will
     * force a save even if there have been no changes to the spec.
     *
     * @param hex160 args.ID
     *
     * @return Promise
     */
    requestSave: function (args) {
        var specData = CBUISpecSaver.specDataByID[args.ID];
        var URL = "/api/?class=CBModels&function=save";

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

        // Make a request.
        function request() {
            var formData = new FormData();
            formData.append("specAsJSON", JSON.stringify(specData.spec));

            return Colby.fetchAjaxResponse(URL, formData).then(handleResolved).catch(handleRejected);
        }

        // When a request is resolved:
        //      1. Update the spec version.
        //      2. If there is a pending request that request will be activated
        //         shortly so remove the pending request flag.
        //      3. If there is no pending request, clear the promise.
        function handleResolved(value) {
            specData.spec.version += 1;

            if (specData.hasPendingRequest) {
                specData.hasPendingRequest = undefined;
            } else {
                specData.promise = undefined;
            }

            return value;
        }

        // When a request is rejected stop all active and pending requests:
        //      1. Reset specData values.
        //      2. Return a rejected promise to avoid executing further resolved
        //         handlers in the promise chain.
        //
        // TODO: Display an error message? Try again?
        function handleRejected(value) {
            specData.hasPendingRequest = undefined;
            specData.promise = undefined;

            return Promise.reject(value);
        }
    },
};

"use strict"; /* jshint strict: global */
/* globals
    Colby */

var CBUISpecSaver = {

    specSavers: {},

    /**
     * @param object args.spec
     *
     * @return {
     *      specChangedCallback: function,
     * }
     */
    create: function (args) {
        if (CBUISpecSaver.specSavers[args.spec.ID]) {
            Colby.alert("This spec already has a specSaver.");
        } else {
            CBUISpecSaver.specSavers[args.spec.ID] = {
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
     * @param hex160 args.ID
     *
     * @return undefined
     */
    specDidChange: function (args) {
        var specSaver = CBUISpecSaver.specSavers[args.ID];

        if (specSaver.timeoutID) {
            clearTimeout(specSaver.timeoutID);
            specSaver.timeoutID = undefined;
        }

        if (!specSaver.saveWasRequested) {
            var callback = CBUISpecSaver.requestSave.bind(undefined, {ID: args.ID});
            specSaver.timeoutID = setTimeout(callback, 5000);
        }
    },

    /**
     * @param hex160 args.ID
     *
     * @return undefined
     */
    requestSave: function (args) {
        var specSaver = CBUISpecSaver.specSavers[args.ID];

        if (specSaver.xhr) {
            specSaver.saveWasRequested = true;
        } else {
            specSaver.saveWasRequested = undefined;

            var formData = new FormData();
            formData.append("specAsJSON", JSON.stringify(specSaver.spec));

            var xhr = new XMLHttpRequest();
            xhr.onerror = CBUISpecSaver.requestSaveDidError.bind(undefined, {ID: args.ID});
            xhr.onload = CBUISpecSaver.requestSaveDidLoad.bind(undefined, {ID: args.ID});
            xhr.open("POST", "/api/?class=CBModels&function=save");
            xhr.send(formData);

            specSaver.xhr = xhr;
        }
    },

    /**
     * @param hex160 args.ID
     *
     * @return undefined
     */
    requestSaveDidError: function (args) {
        var specSaver = CBUISpecSaver.specSavers[args.ID];

        // TODO: how do recover from error? second chance? limited attempts?

        Colby.displayXHRError(specSaver.xhr);
        specSaver.xhr = undefined;
    },

    /**
     * @param hex160 args.ID
     *
     * @return undefined
     */
    requestSaveDidLoad: function (args) {
        var specSaver = CBUISpecSaver.specSavers[args.ID];
        var response = Colby.responseFromXMLHttpRequest(specSaver.xhr);
        specSaver.xhr = undefined;

        if (response.wasSuccessful) {
            specSaver.spec.version += 1;
        } else {
            Colby.displayResponse(response);
        }

        if (specSaver.saveWasRequested) {
            CBUISpecSaver.requestSave({ID: args.ID});
        }
    },
};

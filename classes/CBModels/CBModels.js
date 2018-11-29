"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModels */
/* global
    CBConvert,
    CBModel,
*/

var CBModels = {

    /**
     * @param ID ID
     *
     * @return object|undefined
     */
    fetchSpecFromSessionStorageByID: function (ID) {
        if (CBConvert.valueAsID(ID) === undefined) {
            throw new TypeError("The ID parameter is not valid.");
        }

        let key = CBModels.IDToStorageKey(ID);
        let specAsJSON = sessionStorage.getItem(key);

        if (specAsJSON !== null) {
            return JSON.parse(specAsJSON);
        } else {
            return undefined;
        }
    },

    /**
     * @param ID ID
     *
     * @return string
     */
    IDToStorageKey: function (ID) {
        if (CBConvert.valueAsID(ID) === undefined) {
            throw new TypeError("The ID parameter is not valid.");
        }

        return "ID_" + ID;
    },

    /**
     * @param object spec
     *
     * @return undefined
     */
    saveSpecToSessionStorage: function (spec) {
        let ID = CBModel.valueAsID(spec, "ID");

        if (ID === undefined) {
            throw new Error(
                "The spec does not have a valid ID property value."
            );
        }

        let version = CBModel.valueAsInt(spec, "version") || 0;
        let originalSpec = CBModels.fetchSpecFromSessionStorageByID(ID);
        let originalVersion = CBModel.valueAsInt(originalSpec, 'version') || 0;

        if (version === originalVersion) {
            spec.version = version + 1;

            sessionStorage.setItem(
                CBModels.IDToStorageKey(ID),
                JSON.stringify(spec)
            );
        } else {
            throw new Error(
                "The spec has been saved by another process since you loaded it."
            );
        }
    }
};

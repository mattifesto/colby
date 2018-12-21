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
     *
     *      {
     *          spec: object
     *          meta: object
     *
     *              {
     *                  ID: ID
     *                  version: int
     *              }
     *      }
     */
    fetchFromSessionStorage: function (ID) {
        if (CBConvert.valueAsID(ID) === undefined) {
            throw new TypeError("The ID parameter is not valid.");
        }

        let recordAsJSON = sessionStorage.getItem(
            CBModels.IDToStorageKey(ID)
        );

        if (recordAsJSON !== null) {
            return JSON.parse(recordAsJSON);
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
    saveToSessionStorage: function (ID, spec, version) {
        if (CBConvert.valueAsObject(spec) === undefined) {
            throw new TypeError("The spec parameter is not valid");
        }

        let record = CBModels.fetchFromSessionStorage(ID);
        let recordVersion = CBModel.valueAsInt(record, "meta.version") || 0;
        let now = Date.now();

        if (version === recordVersion) {
            if (version === 0) {
                record = {
                    meta: {
                        ID: ID,
                        created: now,
                        version: 0,
                    },
                };
            }

            record.spec = spec;
            record.meta.modified = now;
            record.meta.version += 1;

            sessionStorage.setItem(
                CBModels.IDToStorageKey(ID),
                JSON.stringify(record)
            );
        } else {
            throw new Error(
                "The spec has been saved by another process since you loaded it."
            );
        }
    }
};

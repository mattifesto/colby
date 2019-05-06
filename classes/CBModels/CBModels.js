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
     * @param Storage storage
     *
     * @return undefined
     */
    delete: function (ID, storage) {
        storage.removeItem(CBModels.IDToStorageKey(ID));
    },
    /* delete() */


    /**
     * @param ID ID
     * @param Storage storage
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
    fetch: function (ID, storage) {
        if (CBConvert.valueAsID(ID) === undefined) {
            throw new TypeError("The ID parameter is not valid.");
        }

        let recordAsJSON = storage.getItem(
            CBModels.IDToStorageKey(ID)
        );

        if (recordAsJSON !== null) {
            return JSON.parse(recordAsJSON);
        } else {
            return undefined;
        }
    },
    /* fetch() */


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
        return CBModels.fetch(ID, sessionStorage);
    },
    /* fetchFromSessionStorage() */


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
    /* IDToStorageKey() */


    /**
     * @param ID ID
     * @param object spec
     * @param int version
     * @param Storage storage
     *
     * @return undefined
     */
    save: function (ID, spec, version, storage) {
        if (CBConvert.valueAsObject(spec) === undefined) {
            throw new TypeError("The spec parameter is not valid");
        }

        let record = CBModels.fetch(ID, storage);
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

            storage.setItem(
                CBModels.IDToStorageKey(ID),
                JSON.stringify(record)
            );
        } else {
            throw new Error(
                "The spec has been saved by another process since you loaded it."
            );
        }
    },
    /* save() */


    /**
     * @param ID ID
     * @param object spec
     * @param int version
     *
     * @return undefined
     */
    saveToSessionStorage: function (ID, spec, version) {
        CBModels.save(ID, spec, version, sessionStorage);
    }
    /* saveToSessionStorage() */
};

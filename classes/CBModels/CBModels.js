"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModels */
/* global
    CBConvert,
    CBException,
    CBModel,
*/



var CBModels = {

    /* -- functions -- -- -- -- -- */



    /**
     * @param ID ID
     * @param Storage storage
     *
     * @return undefined
     */
    delete: function (ID, storage) {
        if (!(storage instanceof Storage)) {
            throw CBException.withError(
                new TypeError(
                    "The \"storage\" parameter must be an instance of the" +
                    " Storage class."
                ),
                "",
                "acca1dfe5b808d6afc8b7d52007367356761b743"
            );
        }

        storage.removeItem(
            CBModels.IDToStorageKey(ID)
        );
    },
    /* delete() */



    /**
     * @param ID ID
     * @param Storage storage
     *
     * @return object|undefined
     *
     *      If the function returns an object, the spec property is guaranteed
     *      to be a model and the meta.version property is guaranteed to be an
     *      integer.
     *
     *      {
     *          spec: object
     *          meta: object
     *
     *              {
     *                  ID: ID
     *                  created: int
     *                  modified: int
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

        if (recordAsJSON === null) {
            return undefined;
        }

        let record = JSON.parse(recordAsJSON);

        /**
         * A record is only valid if its spec property value is a model and
         * its meta.version property value is an integer.
         */

        if (CBModel.valueAsModel(record, "spec") === undefined) {
            return undefined;
        }

        if (CBModel.valueAsInt(record, "meta.version") === undefined) {
            return undefined;
        }

        return record;
    },
    /* fetch() */



    /**
     * @deprecated use CBModels.fetch()
     */
    fetchFromSessionStorage: function (ID) {
        return CBModels.fetch(ID, sessionStorage);
    },
    /* fetchFromSessionStorage() */



    /**
     * This function will fetch and update a spec, but it will not save the
     * updates.
     *
     * @param string ID
     * @param Storage storage
     * @param object updates
     *
     * @return object
     *
     *      {
     *          spec: object
     *          meta: object
     *
     *              {
     *                  ID: ID
     *                  created: int
     *                  modified: int
     *                  version: int
     *              }
     *      }
     */
    fetchAndUpdate: function (ID, storage, updates) {
        let record = CBModels.fetch(ID, storage);

        if (record === undefined) {
            let now = Date.now();

            record = {
                spec: updates,
                meta: {
                    ID: ID,
                    created: now,
                    modified: now,
                    version: 0,
                }
            };
        } else {
            CBModel.merge(
                record.spec,
                updates
            );
        }

        return record;
    },
    /* fetchAndUpdate() */



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
        if (CBConvert.valueAsModel(spec) === undefined) {
            throw CBException.withError(
                new TypeError(
                    "The \"spec\" parameter must be a model."
                ),
                "",
                "08d54c8f19edf030e57d80c0ee436aae6a91083f"
            );
        }

        let now = Date.now();
        let fetchedRecord = CBModels.fetch(ID, storage);

        let fetchedVersion = CBModel.valueAsInt(
            fetchedRecord,
            "meta.version"
        ) || 0;


        if (version === fetchedVersion) {
            let created = CBModel.valueAsInt(
                fetchedRecord,
                "meta.created"
            ) || now;

            let updatedRecord = {
                spec: spec,
                meta: {
                    ID: ID,
                    created: created,
                    modified: now,
                    version: version + 1,
                }
            };

            storage.setItem(
                CBModels.IDToStorageKey(ID),
                JSON.stringify(updatedRecord)
            );
        } else {
            let message =
            "The spec has been saved by another process since you loaded it.";

            throw CBException.withError(
                new Error(message),
                "",
                "1d094e7ef6db1efc327c1b8addd0c7ec758dccd9"
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

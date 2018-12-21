"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelUpdater */
/* global
    CBConvert,
    CBModel,
    CBModels,
*/

var CBModelUpdater = {

    /**
     * @param ID ID
     * @param object updates
     *
     * @return object
     *
     *      {
     *          spec: object (readonly)
     *          save: function
     *      }
     */
    fetchFromSession: function (ID, updates) {
        if (CBConvert.valueAsObject(updates) === undefined) {
            throw new TypeError("The updates parameter is not valid.");
        }

        let record = CBModels.fetchFromSessionStorage(ID);
        let mostRecentlySavedSpec = CBModel.value(record, "spec");
        let workingSpec = CBModel.clone(
            CBModel.valueToObject(record, "spec")
        );
        let workingVersion = CBModel.valueAsInt(record, "meta.version") || 0;

        /**
         * Merge updates with the working spec.
         */
        CBModel.merge(workingSpec, updates);

        /**
         * Release the record and updates objects.
         */
        record = undefined;
        updates = undefined;

        let api = {
            get spec() {
                return workingSpec;
            },
            save: function () {
                if (!CBModel.equals(mostRecentlySavedSpec, workingSpec)) {
                    CBModels.saveToSessionStorage(ID, workingSpec, workingVersion);

                    mostRecentlySavedSpec = CBModel.clone(workingSpec);
                    workingVersion += 1;
                }
            }
        };

        return api;
    },
};

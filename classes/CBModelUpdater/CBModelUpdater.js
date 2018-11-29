"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelUpdater */
/* global
    CBModel,
    CBModels,
*/

var CBModelUpdater = {

    /**
     * @param object updates
     *
     *      {
     *          ID: ID
     *
     *              A valid ID must be specified because the ID is used to fetch
     *              the spec.
     *      }
     *
     * @return object
     *
     *      {
     *          original: mixed
     *          working: object
     *          save: function
     *      }
     */
    fetchFromSession: function (updates) {
        let ID = CBModel.valueAsID(updates, "ID");

        if (ID === undefined) {
            throw new Error(
                "The updates parameter does not have a valid ID property value."
            );
        }

        let originalSpec = CBModels.fetchSpecFromSessionStorageByID(ID);
        let workingSpec = updates;

        if (originalSpec !== undefined) {
            workingSpec = CBModel.clone(originalSpec);

            CBModel.merge(workingSpec, updates);
        }

        let api = {
            get original() {
                return CBModel.clone(originalSpec);
            },
            get working() {
                return workingSpec;
            },
            save: function () {
                if (!CBModel.equals(originalSpec, workingSpec)) {
                    CBModels.saveSpecToSessionStorage(workingSpec);

                    originalSpec = CBModel.clone(workingSpec);
                }
            }
        };

        return api;
    },
};

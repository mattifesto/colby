"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBView */
/* global
    CBModel,
*/

var CBView = {

    /**
     * @param object model
     *
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *      }
     */
    create: function (model) {
        let callable = CBModel.classFunction(model, "CBView_create");

        if (callable === undefined) {
            throw new Error(
                "The function CBView_create() in not callable on the " +
                CBModel.valueToString(model, "className") +
                " object."
            );
        }

        return callable(model);
    },
};

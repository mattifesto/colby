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

        if (callable !== undefined) {
            return callable(model);
        } else {
            let element = document.createComment(
                "The class for a model with the class name \"" +
                CBModel.valueToString(model, "className") +
                "\" has not implemented the CBView_create() interface."
            );

            return {
                get element() {
                    return element;
                }
            };
        }
    },
};

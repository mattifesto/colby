"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModel */
/* global
    CBConvert,
*/

var CBModel = {

    /**
     * @param mixed model
     * @param string keyPath
     *
     * @return mixed
     */
    value: function (model, keyPath) {
        if (model === undefined) {
            return undefined;
        }

        let keys = keyPath.split(".");
        let propertyName = keys.pop();

        for (let i = 0; i < keys.length; i += 1) {
            let key = keys[i];
            model = model[key];

            if (model === undefined) {
                return undefined;
            }
        }

        return model[propertyName];
    },

    /**
     * @param mixed model
     * @param string keyPath
     *
     * @return [mixed]
     */
    valueToArray: function (model, keyPath) {
        return CBConvert.valueToArray(CBModel.value(model, keyPath));
    },

    /**
     * @param mixed model
     * @param string keyPath
     *
     * @return string
     */
    valueToString: function (model, keyPath) {
        return CBConvert.valueToString(CBModel.value(model, keyPath));
    },
};

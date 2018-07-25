"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModel */
/* global
    CBConvert,
*/

var CBModel = {

    /**
     * This function determines if two models are effectively the same. This
     * function enforces the fact that the objects are either models or types
     * that are allowed for model properties. For instance, if the type of a
     * value is "function", false will be returned even if the functions are
     * equal because functions are not allowed property types of models.
     *
     * This function currently does allow loose equality.
     *
     * @param mixed value1
     * @param mixed value2
     *
     * @return bool
     */
    equals: function (value1, value2) {
        let type = typeof value1;

        if (type !== typeof value2) {
            return false;
        }

        if (Array.isArray(value1) && Array.isArray(value2)) {
            if (value1.length !== value2.length) {
                return false;
            }

            return value1.every(function (value, index) {
                return CBModel.equals(value, value2[index]);
            });
        }

        if (Array.isArray(value1) || Array.isArray(value2)) {
            return false;
        }

        if (type === "object") {
            if (value1 === null && value2 === null) {
                return true;
            }

            if (value1 === null || value2 === null) {
                return false;
            }

            let keys1 = Object.keys(value1);
            let keys2 = Object.keys(value2);

            if (keys1.length !== keys2.length) {
                return false;
            }

            for (let i = 0; i < keys1.length; i += 1) {
                let key = keys1[i];

                if (!CBModel.equals(value1[key], value2[key])) {
                    return false;
                }
            }

            return true;
        }

        switch (type) {
            case "boolean":
            case "number":
            case "string":
            case "undefined":
                return value1 === value2;

            default:
                return false;
        }
    },

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

        if (typeof keyPath !== "string") {
            throw new TypeError("The \"keyPath\" argument to the CModel.value() function must be a string.");
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
     * @return Number|undefined
     */
    valueAsInt: function (model, keyPath) {
        return CBConvert.valueAsInt(CBModel.value(model, keyPath));
    },

    /**
     * @param mixed model
     * @param string keyPath
     *
     * @return Object|undefined
     */
    valueAsObject: function (model, keyPath) {
        return CBConvert.valueAsObject(CBModel.value(model, keyPath));
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
     * @return object
     */
    valueToObject: function (model, keyPath) {
        return CBConvert.valueToObject(CBModel.value(model, keyPath));
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

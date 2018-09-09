"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModel */
/* global
    CBConvert,
*/

var CBModel = {

    /**
     * @param model model
     * @param string functionName
     *
     * @return Function|undefined
     */
    classFunction: function (model, functionName) {
        let className = CBModel.valueToString(model, "className");
        let callable = CBModel.value(window, className + "." + functionName);

        if (typeof callable === "function") {
            return callable;
        }

        return undefined;
    },

    /**
     * This function performs a deep clone of a model by serializing it to JSON
     * and then unserializing it. Since models are always serialized to JSON
     * this method of cloning is will always produce a valid clone.
     *
     * @param mixed model
     *
     *      This parameter does not technically have to be a model. It does not
     *      need be an object, have className property, or a valid ID.
     *
     *      It does need to be JSON encodable.
     *
     * @return mixed
     */
    clone: function (model) {
        return JSON.parse(JSON.stringify(model));
    },

    /**
     * This function is similar to Object.keys() except that it will not return
     * keys for explicitly undefined property values. JavaScript objects treat
     * properties explicitly set with an undefined value as set properties.
     *
     * @param mixed value
     *
     *      This function is meant to be used with objects. The return value for
     *      non-objects will be similar the return value of Object.keys().
     *
     * @return [string]
     */
    definedKeys: function (value) {
        let definedKeys = [];
        let keys = Object.keys(value);

        keys.forEach(function (key) {
            if (value[key] !== undefined) {
                definedKeys.push(key);
            }
        });

        return definedKeys;
    },

    /**
     * This function determines if two models or model-like values are equal.
     *
     * Notes:
     *
     *      Explicitly Set Undefined Values: This function will ignore
     *      explicitly set undefined property values.
     *
     *      Strict Equality Only: 1 and "1" are not considered to be equal.
     *
     *      Primitive Object Instances: String, Number, and Boolean object
     *      instances and their primitive values are not considered to be equal.
     *      It is odd behavior to assign a primitive object instance to a
     *      property anyway, so avoid doing this.
     *
     *      JSON Types Only: This function is intended to be used only with
     *      model and model-like values. The existence of a function typed
     *      property value on an object will always make this function return
     *      false.
     *
     * @param mixed value1
     * @param mixed value2
     *
     * @return boolean
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

            let keys1 = CBModel.definedKeys(value1);
            let keys2 = CBModel.definedKeys(value2);

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
     * @return ID|undefined
     */
    valueAsID: function (model, keyPath) {
        return CBConvert.valueAsID(CBModel.value(model, keyPath));
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
     * @return Number|undefined
     */
    valueAsNumber: function (model, keyPath) {
        return CBConvert.valueAsNumber(CBModel.value(model, keyPath));
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

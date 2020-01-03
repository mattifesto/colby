"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModel */
/* global
    CBConvert,
*/

var CBModel = {

    /**
     * @param object model
     * @param string functionName
     *
     * @return function|undefined
     */
    classFunction: function (model, functionName) {
        let className = CBModel.valueToString(model, "className");

        return CBModel.valueAsFunction(
            window[className],
            functionName
        );
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
     * @param object model1
     * @param object model2
     *
     *      The model2 property value will be merged into model1.
     *
     * @return undefined
     */
    merge: function(model1, model2) {
        Object.assign(model1, model2);
    },

    /**
     * @param mixed model
     * @param string keyPath
     *
     * @return mixed
     */
    value: function (model, keyPath) {
        model = CBConvert.valueAsObject(model);

        if (model === undefined) {
            return undefined;
        }

        if (typeof keyPath !== "string") {
            throw TypeError(
                "The keyPath argument must be a string."
            );
        }

        let keys = keyPath.split(".");
        let propertyName = keys.pop();

        for (let i = 0; i < keys.length; i += 1) {
            let key = keys[i];
            model = CBConvert.valueAsObject(model[key]);

            if (model === undefined) {
                return undefined;
            }
        }

        return model[propertyName];
    },
    /* value() */



    /**
     * @param mixed model
     * @param string keyPath
     *
     * @return string|undefined
     */
    valueAsEmail: function (model, keyPath) {
        return CBConvert.valueAsEmail(
            CBModel.value(model, keyPath)
        );
    },



    /**
     * @return function|undefined
     */
    valueAsFunction: function (model, keyPath) {
        return CBConvert.valueAsFunction(
            CBModel.value(model, keyPath)
        );
    },
    /* valueAsFunction() */



    /**
     * @deprecated use CBMode.valueAsCBID()
     */
    valueAsID: function (model, keyPath) {
        return CBModel.valueAsCBID(model, keyPath);
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
     * @return object|undefined
     */
    valueAsModel: function (model, keyPath) {
        return CBConvert.valueAsModel(CBModel.value(model, keyPath));
    },



    /**
     * @param mixed model
     * @param string keyPath
     *
     * @return string|undefined
     */
    valueAsMoniker: function (model, keyPath) {
        return CBConvert.valueAsMoniker(CBModel.value(model, keyPath));
    },



    /**
     * @param mixed model
     * @param string keyPath
     *
     * @return string|undefined
     */
    valueAsName: function (model, keyPath) {
        return CBConvert.valueAsName(
            CBModel.value(model, keyPath)
        );
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
     * @return CBID|undefined
     */
    valueAsCBID: function (model, keyPath) {
        return CBConvert.valueAsCBID(
            CBModel.value(model, keyPath)
        );
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
     * @return bool
     */
    valueToBool: function (model, keyPath) {
        return CBConvert.valueToBool(
            CBModel.value(model, keyPath)
        );
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
/* CBModel */

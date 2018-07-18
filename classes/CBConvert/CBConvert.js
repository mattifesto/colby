"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBConvert */

var CBConvert = {

    /**
     * @param Error error
     *
     * @return string (plain text)
     */
    errorToDescription: function (error) {
        let errorMessage = error.message;

        if (typeof errorMessage !== "string") {
            errorMessage = "no error message is available";
        }

        let stack = CBConvert.errorToStackTrace(error);

        if (stack !== undefined) {
            let entry = stack.split("\n", 1)[0];
            let data = stackTraceEntryToData(entry);

            if (data !== undefined) {
                let basename = pathToBasename(data.fileName) || data.fileName;

                if (data !== undefined) {
                    return '"' +
                        errorMessage +
                        '" in ' +
                        basename +
                        " line " +
                        data.lineNumber;
                }
            }
        }

        return errorMessage;

        /**
         * @NOTE 2017.07.13 imperfect implementaton
         *
         * @param string path
         *
         * @return string|undefined
         */
        function pathToBasename(path) {
            let captures = /^.*\/([^\/]+)$/.exec(path);

            if (captures === undefined) {
                return undefined;
            }

            return captures[1];
        }

        /**
         * @NOTE 2017.07.13 imperfect implementaton
         *
         * @param entry string
         *
         * @return object|undefined
         *
         *      {
         *          functionName: string
         *          fileName: string
         *          lineNumber: int
         *          columnNumber: int
         *      }
         */
        function stackTraceEntryToData(entry) {
            let captures = /^(.*)@(.*):([0-9]+):([0-9]+)$/.exec(entry);

            if (captures === null) {
                return undefined;
            }

            return {
                functionName: captures[1],
                fileName: captures[2],
                lineNumber: captures[3],
                columnNumber: captures[4],
            };
        }
    },

    /**
     * @param Error error
     *
     * @return string|undefined
     */
    errorToStackTrace: function (error) {
        let stackTrace = error.stack;

        if (typeof stackTrace === "string") {
            return stackTrace;
        } else {
            return undefined;
        }
    },

    /**
     * @param mixed value
     *
     * @return Number|undefined
     */
    valueAsInt: function (value) {
        let number = CBConvert.valueAsNumber(value);

        if (Number.isInteger(number)) {
            return number;
        } else {
            return undefined;
        }
    },

    /**
     * Determines whether the value parameter can reasonably be interpreted to
     * be a number.
     *
     * If the value is a Number and is finite, the value will be returned.
     *
     * If the value is a String and its trimmed value is a series of digits with
     * an optional single decimal point, it will be converted to a Number and
     * returned.
     *
     * This function differs from a cast in that boolean and other types will
     * not ever be considered numbers.
     *
     * This function is not localized.
     *
     * @param mixed value
     *
     * @return Number|undefined
     *
     *      If the value is determined to be a number, a Number is returned;
     *      otherwise undefined.
     */
    valueAsNumber: function (value) {
        if (typeof value === "number") {
            return Number.isFinite(value) ? value : undefined;
        }

        if (typeof value === "string") {
            if (value.match(/[0-9]/) !== null) {
                value = value.trim();

                if (value.match(/^-?[0-9]*\.?[0-9]*$/) !== null) {
                    return Number(value);
                }
            }

            return undefined;
        }

        return undefined;
    },

    /**
     * @param mixed value
     *
     * @return [mixed]
     *
     *      If the value parameter is an array it is returned; otherwise an
     *      empty array is returned.
     */
    valueToArray: function (value) {
        if (Array.isArray(value)) {
            return value;
        } else {
            return [];
        }
    },

    /**
     * @param mixed value
     *
     * @return object
     *
     *      If the value parameter is an object it will be returned; otherwise
     *      an empty object will be returned.
     */
    valueToObject: function (value) {
        if (typeof value === "object" && !Array.isArray(value)) {
            return value;
        } else {
            return {};
        }
    },

    /**
     * @param mixed value
     *
     * @return string
     *
     *      The value parameter converted to JSON suitable for display. The
     *      value returned will not always be valid JSON. It will return
     *      "undefined" for the value undefined which is not valid JSON.
     */
    valueToPrettyJSON: function (value) {
        let result = JSON.stringify(value, undefined, 2);

        if (typeof result === "string") {
            return result;
        } else {
            /**
             * JSON.stringify() will return undefined if the value parameter is
             * undefined or other non-convertible values.
             */
            return String(result);
        }
    },

    /**
     * This function exists to support Colby's idea of "to" conversions. To
     * conversions return the desired type, in this case a string, if the
     * original value can reasonably be converted to a string. Otherwise it
     * returns a reasonable default string, which is an empty string.
     *
     * @param mixed value
     *
     * @return string
     *
     *      If the value parameter is a string it will be returned, if it is a
     *      number it will be converted to a string and returned, if it is a
     *      boolean value either "1" or "" will be returned; otherwise "" will
     *      be returned.
     */
    valueToString: function (value) {
        switch (typeof value) {
            case "string":
                return value;

            case "number":
                return String(value);

            case "boolean":
                if (value) {
                    return "1";
                }

                return "";

            default:
                return "";
        }
    },
};

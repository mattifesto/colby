"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBConvert */

var CBConvert = {

    /**
     * @param mixed cents
     *
     *      This parameter must convert to an integer using
     *      CBConvert.valueAsInt().
     *
     * @return string
     *
     *      150         => "1.50"
     *      "5"         => "0.05"
     *      75          => "0.75"
     *      "  3500  "  => "35.00"
     *      " -3500  "  => "-35.00"
     */
    centsToDollars: function (cents) {
        let isNegative = false;
        let centsAsInt = CBConvert.valueAsInt(cents);

        if (centsAsInt === undefined) {
            throw new TypeError("The cents parameter is not a valid integer.");
        }

        if (centsAsInt < 0) {
            isNegative = true;
            centsAsInt = Math.abs(centsAsInt);
        }

        /**
         * Convert to a string.
         */

        let centsAsString = CBConvert.valueToString(centsAsInt);

        /**
         * Pad with zeros until the string is at least 3 digits long.
         */

        while (centsAsString.length < 3) {
            centsAsString = "0" + centsAsString;
        }

        return (
            (isNegative ? "-" : "") +
            centsAsString.substr(0, centsAsString.length - 2) +
            "." +
            centsAsString.substr(-2)
        );
    },

    /**
     * Convert valid dollar amounts, usually from a string, to an integer number
     * of cents.
     *
     * Dollar amounts should not be stored as floating point values because of
     * potential floating point errors. However, user interface elements will
     * sometimes ask for dollar amounts that will be given as a string in the
     * format:
     *
     *      <dollars>.<cents>
     *
     * This function exists mostly to convert those strings into cents integer
     * values.
     *
     * @param mixed dollars
     *
     *      Valid
     *
     *          "-1"
     *          "-1.59"
     *          "0"
     *          "12"
     *          "12.49"
     *          "  12.49 "
     *
     *      Invalid
     *
     *          "1.123"
     *          ""
     *
     * @return Number|undefined
     */
    dollarsAsCents: function (dollars) {
        if (typeof dollars !== "string") {
            dollars = String(dollars);
        }

        dollars = dollars.trim();

        if (dollars.match(/[0-9]/) !== null) {
            let matches = dollars.match(/^(-?)([0-9]*)\.?([0-9]?)([0-9]?)0*$/);

            if (matches !== null) {
                let minusPart = matches[1];
                let dollarsPart = matches[2];
                let cents1Part = matches[3];
                let cents2Part = matches[4];
                let cents = (Number(dollarsPart) * 100) +
                            (Number(cents1Part) * 10) +
                            Number(cents2Part);

                if (minusPart === "-") {
                    cents = -cents;
                }

                return cents;
            }
        }

        return undefined;
    },

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
     * This function is used to convert a string formatted with extra whitespace
     * into a single line. This is useful when making strings readable in code
     * that are going to be used by a function as a single line.
     *
     * Example:
     *
     *      window.alert(
     *          CBConvert.stringToCleanLine(`
     *
     *              I have sometimes sat alone here of an evening, listening,
     *              until I have made the echoes out to be the echoes of all the
     *              footsteps that are coming by and by into our lives.
     *
     *          `)
     *      );
     *
     * @param string value
     *
     * @return string
     */
    stringToCleanLine: function (value) {
        return value.replace(/\s+/, " ").trim();
    },



    /**
     * @param string value
     *
     * @return [string]
     */
    stringToLines: function (value) {
        return value.split(/\r\n|\r|\n/);
    },



    /**
     * @param mixed value
     *
     * @return function|undefined
     */
    valueAsFunction: function (value) {
        if (typeof value === "function") {
            return value;
        } else {
            return undefined;
        }
    },

    /**
     * @param mixed value
     *
     * @return ID|undefined
     */
    valueAsID: function (value) {
        if (typeof value === "string") {
            if (value.match(/^[a-f0-9]{40}$/)) {
                return value;
            }
        }

        return undefined;
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
     * @param mixed value
     *
     * @return object|undefined
     */
    valueAsModel: function (value) {
        if (CBConvert.valueAsObject(value) === undefined) {
            return undefined;
        }

        if (value.className === "" || typeof value.className !== "string") {
            return undefined;
        } else {
            return value;
        }
    },

    /**
     * @param mixed value
     *
     * @return string|undefined
     */
    valueAsMoniker: function (value) {
        let stringValue = CBConvert.valueToString(value).trim();

        if (stringValue.match(/^[a-z0-9_]+$/) !== null) {
            return stringValue;
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

            /**
             * Start by verifing the presence of at least a single number
             * character in the string because the next regular expression does
             * not require a number character to be present on either side of
             * the decimal point.
             *
             * The combination of these two regular expressions guarantees that
             * there is at least one number character either before or after the
             * decimal point.
             */
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
     * This function does not consider null values or arrays to be objects.
     *
     * @param mixed value
     *
     * @return object|undefined
     */
    valueAsObject: function (value) {
        if (
            typeof value === "object" &&
            value !== null &&
            !Array.isArray(value)
        ) {
            return value;
        } else {
            return undefined;
        }
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
     * This function exists to simplify boolean conversions, especially with
     * regard to JSON object property values and strings typed into spreadsheet
     * cells or text fields.
     *
     *      False:
     *
     *          false
     *          null
     *          undefined (JavaScript)
     *          0 or 0.0
     *          trimmed string is "0"
     *          trimmed string is ""
     *
     *      True:
     *
     *          everything else
     *
     * @param mixed value
     *
     * @return bool
     *
     *      If the value parameter is truthy, true is returned; otherwise false.
     */
    valueToBool: function (value) {
        if (typeof value === "string") {
            value = value.trim();

            if (
                value === "" ||
                value === "0"
            ) {
                return false;
            } else {
                return true;
            }
        }

        if (
            value === false ||
            value === 0 ||
            value === null ||
            value === undefined
        ) {
            return false;
        } else {
            return true;
        }
    },

    /**
     * This function does not consider null values or arrays to be objects.
     *
     * @param mixed value
     *
     * @return object
     *
     *      If the value parameter is an object it will be returned; otherwise
     *      an empty object will be returned.
     */
    valueToObject: function (value) {
        let valueAsObject = CBConvert.valueAsObject(value);

        if (valueAsObject === undefined) {
            return {};
        } else {
            return valueAsObject;
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

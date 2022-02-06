/* global
    CBConvert_stringToStubReplacements
*/

(function () {
    "use strict";

    window.CBConvert = {
        centsToDollars: CBConvert_centsToDollars,
        dollarsAsCents: CBConvert_dollarsAsCents,
        errorToCBJavaScriptErrorModel: CBConvert_errorToCBJavaScriptErrorModel,
        errorToDescription: CBConvert_errorToDescription,
        errorToErrorDetails: CBConvert_errorToErrorDetails,
        errorToStackTrace: CBConvert_errorToStackTrace,
        stringToCleanLine: CBConvert_stringToCleanLine,
        stringToLines: CBConvert_stringToLines,
        stringToStub: CBConvert_stringToStub,
        stringToURI: CBConvert_stringToURI,
        valueAsCBID: CBConvert_valueAsCBID,
        valueAsEmail: CBConvert_valueAsEmail,
        valueAsFunction: CBConvert_valueAsFunction,
        valueAsID: CBConvert_valueAsID,
        valueAsInt: CBConvert_valueAsInt,
        valueAsModel: CBConvert_valueAsModel,
        valueAsMoniker: CBConvert_valueAsMoniker,
        valueAsName: CBConvert_valueAsName,
        valueAsNumber: CBConvert_valueAsNumber,
        valueAsObject: CBConvert_valueAsObject,
        valueIsName: CBConvert_valueIsName,
        valueToArray: CBConvert_valueToArray,
        valueToBool: CBConvert_valueToBool,
        valueToObject: CBConvert_valueToObject,
        valueToPrettyJSON: CBConvert_valueToPrettyJSON,
        valueToString: CBConvert_valueToString,
    };



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
    function
    CBConvert_centsToDollars(
        cents
    ) {
        let isNegative = false;
        let centsAsInt = CBConvert_valueAsInt(cents);

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

        let centsAsString = CBConvert_valueToString(centsAsInt);

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
    }
    /* CBConvert_centsToDollars() */



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
    function
    CBConvert_dollarsAsCents(
        dollars
    ) {
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
    }
    /* CBConvert_dollarsAsCents() */



    /**
     * Converts an error object to a CBJavaScriptError model.
     *
     * Properties:
     *
     *      Safari          Firefox         Chrome
     *      ------          -------         ------
     *      column          columnNumber    no
     *      line            lineNumber      no
     *      sourceURL       fileName        no
     *
     * History:
     *
     *      An initial goal was to stringify and Error object and send it to an
     *      ajax function. But when an Error object is stringified it doesn't
     *      serialize all of its properties.
     *
     *      Additional information that is not contained in the Error object is
     *      added to the model returned by this function.
     *
     *      The ErrorEvent object passed to the listener of the "error" event
     *      has some standardized properties that are similar, but not all
     *      errors are handled by an error event listener. The "stack" property
     *      actually contains all the data but has a different format on Chrome
     *      browsers.
     *
     * @param Error error
     *
     * @return object (CBJavaScriptError)
     */
    function
    CBConvert_errorToCBJavaScriptErrorModel(
        error
    ) {
        let errorDetails = CBConvert_errorToErrorDetails(
            error
        );

        return {
            className: 'CBJavaScriptError',
            column: errorDetails.columnNumber,
            line: errorDetails.lineNumber,
            message: error.message,
            pageURL: location.href,
            sourceURL: errorDetails.sourceURL,
            stack: error.stack,
        };
    }
    /* CBConvert_errorToCBJavaScriptErrorModel() */



    /**
     * @param Error error
     *
     * @return string (plain text)
     */
    function
    CBConvert_errorToDescription(
        error
    ) {
        let errorMessage = error.message;

        if (typeof errorMessage !== "string") {
            errorMessage = "no error message is available";
        }

        let stack = CBConvert_errorToStackTrace(error);

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
    }
    /* CBConvert_errorToDescription() */



    /**
     * @param Error error
     *
     * @return object
     *
     *      {
     *          sourceURL: string
     *          lineNumber: int
     *          columnNumber: int
     *      }
     */
    function
    CBConvert_errorToErrorDetails(
        error
    ) {
        let errorDetails = {};

        /* Safari */
        if (
            error.line !== undefined
        ) {
            errorDetails.sourceURL = error.sourceURL;
            errorDetails.lineNumber = error.line;
            errorDetails.columnNumber = error.column;
        }

        /* Firefox */
        else if (
            error.lineNumber !== undefined
        ) {
            errorDetails.sourceURL = error.fileName;
            errorDetails.lineNumber = error.lineNumber;
            errorDetails.columnNumber = error.columnNumber;
        }

        /* Chrome */
        else if (
            typeof error.stack === "string"
        ) {
            let stackLines = error.stack.split(
                "\n"
            );

            /**
             * The first line is the error message, the second is the code
             * location.
             */
            if (
                stackLines.length < 2
            ) {
                return errorDetails;
            }

            let stackLine = stackLines[1];

            let matches = stackLine.match(
                /\s*at (.*) \((.+):([0-9]+):([0-9]+)\)$/
            );

            if (
                matches !== null
            ) {
                errorDetails.sourceURL = matches[2];
                errorDetails.lineNumber = matches[3];
                errorDetails.columnNumber = matches[4];
            }
        }

        return errorDetails;
    }
    /* CBConvert_errorToErrorDetails() */



    /**
     * @param Error error
     *
     * @return string|undefined
     */
    function
    CBConvert_errorToStackTrace(
        error
    ) {
        let stackTrace = error.stack;

        if (typeof stackTrace === "string") {
            return stackTrace;
        } else {
            return undefined;
        }
    }
    /* CBConvert_errorToStackTrace() */



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
    function
    CBConvert_stringToCleanLine(
        value
    ) {
        return value.replace(/\s+/g, " ").trim();
    }
    /* CBConvert_stringToCleanLine() */



    /**
     * @param string value
     *
     * @return [string]
     */
    function
    CBConvert_stringToLines(
        value
    ) {
        return value.split(/\r\n|\r|\n/);
    }
    /* CBConvert_stringToLines() */



    /**
     * @param string originalString
     *
     * @return string
     */
    function
    CBConvert_stringToStub(
        originalString
    ) {
        let stub = CBConvert_valueToString(
            originalString
        );

        stub = stub.toLowerCase();

        CBConvert_stringToStubReplacements.forEach(
            function (stubReplacement) {
                let expression = new RegExp(
                    stubReplacement.pattern,
                    'g'
                );

                stub = stub.replace(
                    expression,
                    stubReplacement.replacement
                );
            }
        );

        return stub;
    }
    /* CBConvert_stringToStub() */



    /**
     * @param string originalString
     *
     * @return string
     */
    function
    CBConvert_stringToURI(
        originalString
    ) {
        originalString = CBConvert_valueToString(
            originalString
        );

        let originalStubs = originalString.split(
            "/"
        );

        let stubs = [];

        originalStubs.forEach(
            function (originalStub) {
                let stub = CBConvert_stringToStub(
                    originalStub
                );

                if (stub !== "") {
                    stubs.push(
                        stub
                    );
                }
            }
        );

        let URI = stubs.join(
            "/"
        );

        return URI;
    }
    /* CBConvert_stringToURI() */



    /**
     * @param mixed value
     *
     * @return CBID|undefined
     */
    function
    CBConvert_valueAsCBID(
        value
    ) {
        if (typeof value === "string") {
            if (value.match(/^[a-f0-9]{40}$/)) {
                return value;
            }
        }

        return undefined;
    }
    /* CBConvert_valueAsCBID() */



    /**
     * @param mixed value
     *
     * @return string|undefined
     */
    function
    CBConvert_valueAsEmail(
        value
    ) {
        let email = CBConvert_valueToString(
            value
        ).trim();

        if (/^\S+@\S+\.\S+$/.test(email)) {
            return email;
        } else {
            return undefined;
        }
    }
    /* CBConvert_valueAsEmail() */



    /**
     * @param mixed value
     *
     * @return function|undefined
     */
    function
    CBConvert_valueAsFunction(
        value
    ) {
        if (typeof value === "function") {
            return value;
        } else {
            return undefined;
        }
    }
    /* CBConvert_valueAsFunction() */



    /**
     * @deprecated use CBConvert.valueAsCBID()
     */
    function
    CBConvert_valueAsID(
        value
    ) {
        return CBConvert_valueAsCBID(value);
    }
    /* CBConvert_valueAsID() */



    /**
     * @param mixed value
     *
     * @return Number|undefined
     */
    function
    CBConvert_valueAsInt(
        value
    ) {
        let number = CBConvert_valueAsNumber(value);

        if (Number.isInteger(number)) {
            return number;
        } else {
            return undefined;
        }
    }
    /* CBConvert_valueAsInt() */



    /**
     * @param mixed value
     * @param string|[string]|undefined classNames
     *
     *      If one or more class names is specified, a model will only be
     *      returned if its class name is one of the specified class names.
     *
     * @return object|undefined
     */
    function
    CBConvert_valueAsModel(
        value,
        classNames
    ) {
        if (
            classNames === undefined
        ) {
            classNames = [];
        }

        else if (
            !Array.isArray(
                classNames
            )
        ) {
            classNames = [classNames];
        }

        let potentialModel = CBConvert_valueAsObject(
            value
        );

        if (
            potentialModel === undefined
        ) {
            return undefined;
        }

        if (
            !CBConvert_valueIsName(
                potentialModel.className
            )
        ) {
            return undefined;
        }

        if (
            classNames.length > 0 &&

            !classNames.includes(
                potentialModel.className
            )
        ) {
            return undefined;
        }

        return potentialModel;
    }
    /* CBConvert_valueAsModel() */



    /**
     * @deprecated user CBConvert.valueAsName()
     *
     * @param mixed value
     *
     * @return string|undefined
     */
    function
    CBConvert_valueAsMoniker(
        value
    ) {
        let stringValue = CBConvert_valueToString(value).trim();

        if (stringValue.match(/^[a-z0-9_]+$/) !== null) {
            return stringValue;
        } else {
            return undefined;
        }
    }
    /* CBConvert_valueAsMoniker() */



    /**
     * @param mixed value
     *
     * @return string|undefined
     */
    function
    CBConvert_valueAsName(
        value
    ) {
        let potentialName = CBConvert_valueToString(
            value
        ).trim();

        if (CBConvert_valueIsName(potentialName)) {
            return potentialName;
        } else {
            return undefined;
        }
    }
    /* CBConvert_valueAsName() */



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
    function
    CBConvert_valueAsNumber(
        value
    ) {
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
    }
    /* CBConvert_valueAsNumber() */



    /**
     * This function does not consider null values or arrays to be objects.
     *
     * @param mixed value
     *
     * @return object|undefined
     */
    function
    CBConvert_valueAsObject(
        value
    ) {
        if (
            typeof value === "object" &&
            value !== null &&
            !Array.isArray(value)
        ) {
            return value;
        } else {
            return undefined;
        }
    }
    /* CBConvert_valueAsObject() */



    /**
     * @param mixed value
     *
     * @return bool
     */
    function
    CBConvert_valueIsName(
        value
    ) {
        if (
            typeof value !== "string"
        ) {
            return false;
        }

        return /^[a-zA-Z0-9_]+$/.test(
            value
        );
    }
    /* CBConvert_valueIsName() */



    /**
     * @param mixed value
     *
     * @return [mixed]
     *
     *      If the value parameter is an array it is returned; otherwise an
     *      empty array is returned.
     */
    function
    CBConvert_valueToArray(
        value
    ) {
        if (Array.isArray(value)) {
            return value;
        } else {
            return [];
        }
    }
    /* CBConvert_valueToArray() */



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
    function
    CBConvert_valueToBool(
        value
    ) {
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
    }
    /* CBConvert_valueToBool() */



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
    function
    CBConvert_valueToObject(
        value
    ) {
        let valueAsObject = CBConvert_valueAsObject(value);

        if (valueAsObject === undefined) {
            return {};
        } else {
            return valueAsObject;
        }
    }
    /* CBConvert_valueToObject() */



    /**
     * @param mixed value
     *
     * @return string
     *
     *      The value parameter converted to JSON suitable for display. The
     *      value returned will not always be valid JSON. It will return
     *      "undefined" for the value undefined which is not valid JSON.
     */
    function
    CBConvert_valueToPrettyJSON(
        value
    ) {
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
    }
    /* CBConvert_valueToPrettyJSON() */



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
    function
    CBConvert_valueToString(
        value
    ) {
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
    }
    /* CBConvert_valueToString() */

})();

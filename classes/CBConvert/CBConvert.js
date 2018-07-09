"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBConvert */

var CBConvert = {

    /**
     * @param mixed value
     *
     * @return string
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

"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBModel,
    CBUIStringEditor,
*/



var CBUIUnixTimestampEditor = {

    /**
     * @param object args
     *
     *      {
     *          labelText: string
     *          propertyName: string
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *          updateWithJavaScriptTimestamp: function
     *      }
     */
    create(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let labelText = CBModel.valueToString(
            args,
            "labelText"
        );

        let propertyName = CBModel.valueToString(
            args,
            "propertyName"
        );

        var dateStringSpec = {};

        var dateStringEditor = CBUIStringEditor.createEditor(
            {
                labelText,
                placeholderText: "YYYY/MM/DD HH:MM AM",
                propertyName: "value",
                spec: dateStringSpec,
                specChangedCallback: dateStringSpecChanged,
            }
        );

        refresh();

        return {
            element: dateStringEditor.element,
            refresh,
        };



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function dateStringSpecChanged() {
            dateStringEditor.element.style.backgroundColor = "";

            var unixTimestamp;

            if (dateStringSpec.value.trim() !== "") {
                unixTimestamp = (
                    CBUIUnixTimestampEditor.dateStringToUnixTimestamp(
                        dateStringSpec.value
                    )
                );

                if (unixTimestamp === undefined) {
                    dateStringEditor.element.style.backgroundColor = (
                        "hsl(0, 100%, 95%)"
                    );

                    return;
                }
            }

            spec[propertyName] = unixTimestamp;
            specChangedCallback();
        }
        /* dateStringSpecChanged() */



        /**
         * @return undefined
         */
        function refresh() {
            if (spec[propertyName] !== undefined) {
                dateStringSpec.value = (
                    CBUIUnixTimestampEditor.unixTimestampToDateString(
                        spec[propertyName]
                    )
                );
            } else {
                dateStringSpec.value = undefined;
            }

            dateStringEditor.refresh();
        }
        /* refresh() */

    },
    /* create() */



    /**
     * @param string dateString
     *
     * @return int|undefined
     *
     *      can't decide whether to return "unknown" or not, it's
     *
     *      Empty strings or strings of all whitespace return `undefined`.
     *      Strings that aren't parseable return "unknown".
     */
    dateStringToUnixTimestamp(
        dateString
    ) {
        var matches;

        var patterns = [
            /^\s*([0-9]{4})\/\s*([0-9]+)\s*\/\s*([0-9]+)\s*()()()()$/,
            /^\s*([0-9]{4})\/\s*([0-9]+)\s*\/\s*([0-9]+)\s+([0-9]+):([0-5][0-9])\s*()()$/,
            /^\s*([0-9]{4})\/\s*([0-9]+)\s*\/\s*([0-9]+)\s+([0-9]+):([0-5][0-9])\s*()([aApP][mM])\s*$/,
        ];

        for (var i = 0; i < patterns.length; i++) {
            matches = dateString.match(patterns[i]);

            if (matches) {
                break;
            }
        }

        if (matches === null) {
            return undefined;
        }

        var year = +matches[1];
        var month = matches[2] - 1;
        var day = +matches[3];
        var hours = +matches[4];
        var minutes = +matches[5];
        var seconds = +matches[6];
        var ampm = matches[7].toLowerCase();

        if (ampm !== "") {
            if (hours < 1 || hours > 12) {
                return undefined;
            }

            if (ampm === "am") {
                if (hours == 12) {
                    hours = 0;
                }
            }

            if (ampm === "pm") {
                if (hours != 12) {
                    hours += 12;
                }
            }
        }

        if (
            month < 0 ||
            month > 11 ||
            day > 31 ||
            hours > 23 ||
            minutes > 59
        ) {
            return undefined;
        }

        var date = new Date(year, month, day, hours, minutes, seconds);

        if (isNaN(date)) {
            return undefined;
        } else {
            return Math.floor(
                date.getTime() / 1000
            );
        }
    },
    /* dateStringToUnixTimestamp() */



    /**
     * @param mixed value
     *
     *      If the valus is numberic, it will be treated as a Unix timestamp and
     *      converted to a date string. If not, and empty string will be
     *      returned.
     *
     * @return string
     *
     *      2017.10.12 3:14 PM
     */
    unixTimestampToDateString(
        unixTimestamp
    ) {
        var date = new Date(Math.floor(unixTimestamp * 1000));

        if (isNaN(date)) {
            return "";
        } else {
            var ampm;
            var year = String(date.getFullYear());
            var month = ("00" + (date.getMonth() + 1)).slice(-2);
            var day = ("00" + date.getDate()).slice(-2);
            var hours = date.getHours();

            if (hours < 12) {
                ampm = "am";

                if (hours == 0) {
                    hours = "12";
                }
            } else {
                ampm = "pm";

                if (hours > 12) {
                    hours -= 12;
                }
            }

            hours = ("00" + hours).slice(-2);

            var minutes = (
                "00" +
                date.getMinutes()
            ).slice(-2);

            return (
                year +
                "/" +
                month +
                "/" +
                day +
                " " +
                hours +
                ":" +
                minutes +
                " " +
                ampm
            );
        }
    },
    /* unixTimestampToDateString() */

};

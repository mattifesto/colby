"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported ColbyTests */
/* global
    Colby,
*/

var ColbyTests = {

    /* -- tests -- -- -- -- -- */

    /**
     * @return undefined
     */
    CBTest_dateToString: function () {
        var now = new Date('2012/12/16 10:51 pm');
        var tests = [];

        tests.push({
            'date' : now,
            'string' : '0 seconds ago'
        });

        tests.push({
            'date' : new Date(now.getTime() - 1000),
            'string' : '1 seconds ago'
        });

        tests.push({
            'date' : new Date(now.getTime() - (1000 * 59)),
            'string' : '59 seconds ago'
        });

        tests.push({
            'date' : new Date(now.getTime() - (1000 * 60)),
            'string' : '1 minute ago'
        });

        tests.push({
            'date' : new Date(now.getTime() - (1000 * 60 * 2)),
            'string' : '2 minutes ago'
        });

        tests.push({
            'date' : new Date(now.getTime() - (1000 * 60 * 59)),
            'string' : '59 minutes ago'
        });

        tests.push({
            'date' : new Date('2012/12/16 9:51 pm'),
            'string' : 'Today at 9:51 p.m.'
        });

        tests.push({
            'date' : new Date('2012/12/16 12:00 am'),
            'string' : 'Today at 12:00 a.m.'
        });

        tests.push({
            'date' : new Date('2012/12/15 11:59 pm'),
            'string' : 'Yesterday at 11:59 p.m.'
        });

        tests.push({
            'date' : new Date('2012/12/15 12:00 am'),
            'string' : 'Yesterday at 12:00 a.m.'
        });

        tests.push({
            'date' : new Date('2012/12/14 1:53 pm'),
            'string' : 'December 14, 2012 1:53 p.m.'
        });

        tests.forEach(function (test) {
            var string = Colby.dateToRelativeLocaleString(test.date, now);

            if (string != test.string) {
                throw new Error("Expected: \"" +
                                test.string +
                                "\" but received: \"" +
                                string +
                                "\"");
            }
        });

        return {
            succeeded: true,
        };
    },
    /* CBTest_dateToString() */


    /**
     * @return Promise
     */
    CBTest_displayAndReportError: function () {
        let testDidPass = false;

        return Colby.callAjaxFunction(
            "CBUnitTests",
            "errorTest"
        ).catch(
            function (error) {
                Colby.displayAndReportError(error);

                testDidPass = true;
            }
        ).then(
            function () {
                if (testDidPass) {
                    return {
                        succeeded: true,
                    };
                } else {
                    return {
                        succeeded: false,
                    };
                }
            }
        );
    },
    /* CBTest_displayAndReportError() */


    /**
     * @return undefined
     */
    CBTest_random160: function () {
        let count = 10;
        let values = [];

        for (let i = 0; i < count; i++) {
            values.push(Colby.random160());
        }

        for (let i = 0; i < count; i++) {
            let value = values[i];

            if (!/[a-f0-9]{40}/.test(value)) {
                throw new Error(`Value ${i} is not a hex160: "${value}"`);
            }

            for (let j = i + 1; j < count; j++) {
                if (value === values[j]) {
                    throw Error(
                        `Value ${i} "${value}" is the same as value` +
                        ` ${j} "${values[j]}" which is extremely unlikely` +
                        ` and probably represents an error.`
                    );
                }
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_random160() */
};
/* ColbyTests */

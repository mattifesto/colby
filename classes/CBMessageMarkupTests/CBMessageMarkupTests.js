"use strict";
/* jshint strict: global */
/* exported CBMessageMarkupTests */
/* globals
    CBMessageMarkup,
    CBMessageMarkupTests_html1,
    CBMessageMarkupTests_markup1 */

var CBMessageMarkupTests = {

    /**
     * @return undefined
     */
    markupToHTMLTest: function () {
        var html1 = CBMessageMarkup.markupToHTML(CBMessageMarkupTests_markup1);
        var expectedLines = CBMessageMarkupTests_html1.split(/\r?\n/);
        var resultLines = html1.split(/\r?\n/);

        expectedLines.forEach(function (expectedLine, index) {
            if (resultLines.length < index + 1) {
                throw new Error("The result does not have the expected line number " + index);
            }

            var resultLine = resultLines[index];

            if (expectedLine !== resultLine) {
                throw new Error("Line " +
                                index +
                                " was expected to be \"" +
                                expectedLine +
                                "\" but is actually: \"" +
                                resultLine +
                                "\"");
            }
        });

        if (resultLines.length > expectedLines.length) {
            throw new Error("The result has more lines than were expected");
        }
    },
};

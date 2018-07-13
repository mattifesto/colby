"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBMessageMarkupTests */
/* globals
    CBMessageMarkup,
    CBMessageMarkupTests_html1,
    CBMessageMarkupTests_markup1,
    CBMessageMarkupTests_text1,
*/

var CBMessageMarkupTests = {

    /**
     * @param string string1
     * @param string string2
     *
     * @return undefined
     */
    compareStringsLineByLine: function(string1, string2) {
        var string1Lines = string1.split(/\r?\n/);
        var string2Lines = string2.split(/\r?\n/);

        string1Lines.forEach(function (string1Line, index) {
            if (string2Lines.length < index + 1) {
                throw new Error("Line " +
                                index +
                                " of string 1 is " +
                                string1Line +
                                " but doesn't exist in string 2");
            }

            var string2Line = string2Lines[index];

            if (string1Line !== string2Line) {
                throw new Error("String 1 line " +
                                index +
                                " is \"" +
                                string1Line +
                                "\" which doesn't match string 2 line " +
                                index +
                                " of \"" +
                                string2Line +
                                "\"");
            }
        });

        if (string2Lines.length > string1Lines.length) {
            throw new Error("String 2 has more lines than string 1");
        }
    },

    /**
     * @return undefined
     */
    markupToHTMLTest: function () {
        var expected = CBMessageMarkupTests_html1;
        var result = CBMessageMarkup.markupToHTML(CBMessageMarkupTests_markup1);

        CBMessageMarkupTests.compareStringsLineByLine(expected, result);

        return {
            succeeded: true,
        };
    },

    /**
     * @return undefined
     */
    markupToTextTest: function () {
        var expected = CBMessageMarkupTests_text1;
        var result = CBMessageMarkup.markupToText(CBMessageMarkupTests_markup1);

        CBMessageMarkupTests.compareStringsLineByLine(expected, result);

        return {
            succeeded: true,
        };
    },

    /**
     * @NOTE
     *
     *      CBMessageMarkup.messageToText() always puts a new line at the end of
     *      the last line whether one was originally there or not.
     *
     * @return undefined
     */
    singleLineMarkupToTextTest: function () {
        {
            let singleLineMarkup = "This \(is \- the - result)!";
            let expected = "This (is - the - result)!\n";
            let result = CBMessageMarkup.messageToText(singleLineMarkup);

            CBMessageMarkupTests.compareStringsLineByLine(expected, result);
        }

        {
            let singleLineMarkup = "This is an ID: (9b44a390fcc6d188862ea616940d1860d0a1ee4f (code))";
            let expected = "This is an ID: 9b44a390fcc6d188862ea616940d1860d0a1ee4f\n";
            let result = CBMessageMarkup.messageToText(singleLineMarkup);

            CBMessageMarkupTests.compareStringsLineByLine(expected, result);
        }

        return {
            succeeded: true,
        };
    },

    /**
     * @return undefined
     */
    stringToMarkupTest: function () {
        var string = `
--- div
--- hi
    ---
(hi (strong))
( \\( \\\\(
) \\) \\\\)
`;

        var expected = `
\\-\\-\\- div
\\-\\-\\- hi
    \\-\\-\\-
\\(hi \\(strong\\)\\)
\\( \\\\\\( \\\\\\\\\\(
\\) \\\\\\) \\\\\\\\\\)
`;

        var result = CBMessageMarkup.stringToMarkup(string);

        CBMessageMarkupTests.compareStringsLineByLine(expected, result);

        return {
            succeeded: true,
        };
    }
};

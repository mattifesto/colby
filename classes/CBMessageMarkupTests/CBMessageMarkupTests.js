"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBMessageMarkupTests */
/* globals
    CBConvert,
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
    },

    /* -- functions -- -- -- -- -- */

    /**
     * @param string title
     * @param string actualResult
     * @param string expectedResult
     *
     * @return object
     */
    textResultMismatchFailure: function (
        testTitle,
        actualResult,
        expectedResult
    ) {
        let message;
        let actualResultLines = CBConvert.stringToLines(actualResult);
        let expectedResultLines = CBConvert.stringToLines(expectedResult);

        for (let index = 0; index < actualResultLines.length; index += 1) {
            let lineNumber = index + 1;
            let actualResultLine = actualResultLines[index];
            let expectedResultLine = expectedResultLines[index];

            if (expectedResultLine === undefined) {
                message = `

                    The actual result has more lines than the expected result.

                `;

                break;
            }

            if (actualResultLine !== expectedResultLine) {
                let actualResultLineAsMessage = CBMessageMarkup.stringToMessage(
                    actualResultLine
                );

                let expectedResultLineAsMessage = CBMessageMarkup.stringToMessage(
                    expectedResultLine
                );

                message = `

                    Line ${lineNumber} of the actual result does not match the
                    expected result.

                    --- dl
                        --- dt
                        actual line
                        ---
                        --- dd
                            --- pre\n${actualResultLineAsMessage}
                            ---
                        ---
                        --- dt
                        expected line
                        ---
                        --- dd
                            --- pre\n${expectedResultLineAsMessage}
                            ---
                        ---
                    ---

                `;

                break;
            }
        }

        if (
            message === undefined &&
            actualResultLines.length < expectedResultLines.length
        ) {
            message = `

                The actual result has less lines than the expected result.

            `;
        }

        let actualResultAsMessage = CBMessageMarkup.stringToMessage(
            actualResult
        );
        let expectedResultAsMessage = CBMessageMarkup.stringToMessage(
            expectedResult
        );
        message = `

            ${message}

            --- dl
                --- dt
                Test Title
                ---
                ${testTitle}

                --- dt
                Actual Result
                ---
                --- dd
                    --- pre\n${actualResultAsMessage}
                    ---
                ---

                --- dt
                Expected Result
                ---
                --- dd
                    --- pre\n${expectedResultAsMessage}
                    ---
                ---
            ---

        `;

        return {
            succeeded: false,
            message: message,
        };
    },
};

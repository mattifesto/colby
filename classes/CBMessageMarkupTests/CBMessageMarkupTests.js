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
    CBMessageMarkupTests_paragraphToText_originalValue,
    CBMessageMarkupTests_paragraphToText_expectedResult,
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
     * @return object
     */
    CBTest_messageToHTML: function () {
        let expectedResult = CBMessageMarkupTests_html1;
        let actualResult = CBMessageMarkup.messageToHTML(
            CBMessageMarkupTests_markup1
        );

        if (actualResult !== expectedResult) {
            return CBMessageMarkupTests.textResultMismatchFailure(
                'test 1',
                actualResult,
                expectedResult
            );
        } else {
            return {
                succeeded: true,
            };
        }
    },

    /**
     * @return object
     */
    CBTest_messageToText: function () {
        let actualResult = CBMessageMarkup.markupToText(
            CBMessageMarkupTests_markup1
        );

        let expectedResult = CBMessageMarkupTests_text1;

        if (actualResult !== expectedResult) {
            return CBMessageMarkupTests.textResultMismatchFailure(
                'test 1',
                actualResult,
                expectedResult
            );
        } else {
            return {
                succeeded: true,
            };
        }
    },

    /**
     * @return object
     */
    CBTest_paragraphToText: function () {
        let actualResult = CBMessageMarkup.paragraphToText(
            CBMessageMarkupTests_paragraphToText_originalValue
        );

        let expectedResult = (
            CBMessageMarkupTests_paragraphToText_expectedResult
        );

        if (actualResult !== expectedResult) {
            return CBMessageMarkupTests.textResultMismatchFailure(
                'test 1',
                actualResult,
                expectedResult
            );
        } else {
            return {
                succeeded: true,
            };
        }
    },

    /**
     * @NOTE 2019_02_07
     *
     *      Before today CBMessageMarkup.messageToText() would always put a new
     *      line at the end of the last line whether one was originally there or
     *      not. Now it never puts a new line at the end of the last line.
     *
     *      This is not because of my strong feelling, but the behavior changed
     *      for other reasons and this test started failing. I think maybe it
     *      makes more sense not to add a new line at the end of the last line,
     *      especially in the context of a single line input.
     *
     * @return object
     */
    CBTest_singleLineMarkupToText: function () {
        {
            let singleLineMarkup = "This \(is \- the - result)!";
            let actualResult = CBMessageMarkup.messageToText(singleLineMarkup);
            let expectedResult = "This (is - the - result)!";

            if (actualResult !== expectedResult) {
                return CBMessageMarkupTests.textResultMismatchFailure(
                    'test 1',
                    actualResult,
                    expectedResult
                );
            }
        }

        {
            let singleLineMarkup = "This is an ID: (9b44a390fcc6d188862ea616940d1860d0a1ee4f (code))";
            let actualResult = CBMessageMarkup.messageToText(singleLineMarkup);
            let expectedResult = "This is an ID: 9b44a390fcc6d188862ea616940d1860d0a1ee4f";

            if (actualResult !== expectedResult) {
                return CBMessageMarkupTests.textResultMismatchFailure(
                    'test 2',
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },

    /**
     * @return undefined
     */
    CBTest_stringToMarkup: function () {
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
/* CBMessageMarkupTests */

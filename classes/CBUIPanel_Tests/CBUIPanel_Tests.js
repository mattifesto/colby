"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* exported CBUIPanel_Tests */
/* global
    CBConvert,
    CBModel,
    CBTest,
    CBUI,
    CBUIPanel,
*/

var CBUIPanel_Tests = {

    /**
     * @return Promise
     */
    CBTest_confirmText_cancel: function () {
        return new Promise(closure_execute);



        /* -- closures -- -- -- -- -- */


        /**
         * @return undefined
         */
        function closure_execute(resolve, reject) {
            try {
                let originalPromise = CBUIPanel.confirmText(
                    "This is the cancellation test."
                );

                originalPromise.then(
                    function (response) {
                        if (response) {
                            resolve(
                                {
                                    succeeded: false,
                                }
                            );
                        } else {
                            resolve(
                                {
                                    succeeded: true,
                                }
                            );
                        }
                    }
                ).catch(
                    function (error) {
                        reject(error);
                    }
                );

                window.setTimeout(
                    function () {
                        try {
                            originalPromise.CBUIPanel.cancel();
                        } catch (error) {
                            reject(error);
                        }
                    },
                    100
                );
            } catch (error) {
                reject(error);
            }
        }
        /* closure_execute() */

    },
    /* CBTest_confirmText_cancel() */



    /**
     * @return Promise
     */
    CBTest_confirmText_confirm: function () {
        return new Promise(closure_execute);



        /* -- closures -- -- -- -- -- */


        /**
         * @return undefined
         */
        function closure_execute(resolve, reject) {
            try {
                let originalPromise = CBUIPanel.confirmText(
                    "This is the confirmation test."
                );

                originalPromise.then(
                    function (response) {
                        if (response) {
                            resolve(
                                {
                                    succeeded: true,
                                }
                            );
                        } else {
                            resolve(
                                {
                                    succeeded: false,
                                }
                            );
                        }
                    }
                ).catch(
                    function (error) {
                        reject(error);
                    }
                );

                window.setTimeout(
                    function () {
                        try {
                            originalPromise.CBUIPanel.confirm();
                        } catch (error) {
                            reject(error);
                        }
                    },
                    100
                );
            } catch (error) {
                reject(error);
            }
        }
        /* closure_execute() */

    },
    /* CBTest_confirmText_confirm() */



    /**
     * @return Promise
     */
    CBTest_confirmText_interactive: function () {
        return new Promise(closure_execute);



        /* -- closures -- -- -- -- -- */


        /**
         * @return undefined
         */
        function closure_execute(resolve, reject) {
            try {
                let originalPromise = CBUIPanel.confirmText(
                    "This is the interactive CBUIPanel.confirmText() test."
                );

                originalPromise.then(
                    function (response) {
                        resolve(
                            {
                                message: response ? "confirmed" : "cancelled",
                                succeeded: true,
                            }
                        );
                    }
                ).catch(
                    function (error) {
                        reject(error);
                    }
                );
            } catch (error) {
                reject(error);
            }
        }
        /* closure_execute() */

    },
    /* CBTest_confirmText_interactive() */



    /**
     * @return Promise -> object
     */
    CBTest_displayAjaxResponse_threeTimes:
    async function () {
        let ajaxResponses = [
            {
                message: "1/3 | CBTest_displayAjaxResponse_threeTimes",
            },
            {
                message: "2/3 | CBTest_displayAjaxResponse_threeTimes",
            },
            {
                message: "3/3 | CBTest_displayAjaxResponse_threeTimes",
            },
        ];

        for (
            let index = 0;
            index < ajaxResponses.length;
            index += 1
        ) {
            let ajaxResponse = ajaxResponses[index];

            let controller = CBUIPanel.displayAjaxResponse2(
                ajaxResponse
            );

            await zeroTimeout();

            controller.CBUIPanel_close();
        }

        return {
            succeeded: true,
        };



        /**
         * @return Promise -> undefined
         */
        function zeroTimeout() {
            return new Promise(
                function (resolve) {
                    setTimeout(resolve, 1000);
                }
            );
        }
        /* zeroTimeout() */

    },
    /* CBTest_displayAjaxResponse_threeTimes() */



    /**
     * @return Promise -> object
     */
    CBTest_displayAjaxResponse_threeTimes_interactive:
    async function () {
        let ajaxResponses = [
            {
                message: (
                    "1/3 | CBTest_displayAjaxResponse_threeTimes_interactive"
                ),
            },
            {
                message: (
                    "2/3 | CBTest_displayAjaxResponse_threeTimes_interactive"
                ),
            },
            {
                message: (
                    "3/3 | CBTest_displayAjaxResponse_threeTimes_interactive"
                ),
            },
        ];

        for (
            let index = 0;
            index < ajaxResponses.length;
            index += 1
        ) {
            let ajaxResponse = ajaxResponses[index];

            await CBUIPanel.displayAjaxResponse(
                ajaxResponse
            );
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_displayAjaxResponse_threeTimes_interactive() */



    /**
     * @return object
     */
    CBTest_displayElement_alreadyDisplayedError: function() {
        let contentElement = document.createElement("div");
        contentElement.textContent = "content element";

        CBUIPanel.displayElement(contentElement);

        let actualSourceID;
        let expectedSourceID = "44a2f3c7d22095385de52b49193dea07b004fee3";

        try {
            CBUIPanel.displayElement(contentElement);
        } catch (error) {
            actualSourceID = CBModel.valueAsID(
                error,
                "CBException.sourceID"
            );
        }

        CBUIPanel.hidePanelWithContentElement(
            contentElement
        );

        if (actualSourceID !== expectedSourceID) {
            return CBTest.resultMismatchFailure(
                CBConvert.stringToCleanLine(`

                    Verify the source ID of the error thrown when attempting to
                    call CBUIPanel.displayElement() with an element that is
                    already being displayed.

                `),
                actualSourceID,
                expectedSourceID
            );
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_displayElement_alreadyDisplayedError() */



    /**
     * @return object
     */
    CBTest_displayElementThreeTimes_interactive(
    ) {
        display("one", "red");
        display("two", "green");
        display("three", "blue");

        return {
            succeeded: true,
        };



        /* -- closures -- -- -- -- -- */



        function
        display(
            text,
            color
        ) {
            let contentElement = CBUI.createElement();
            contentElement.style.backgroundColor = color;
            contentElement.textContent = text;

            contentElement.addEventListener(
                "click",
                function clickEventListener() {
                    CBUIPanel.hidePanelWithContentElement(
                        contentElement
                    );
                }
            );

            CBUIPanel.displayElement(
                contentElement
            );
        }
        /* display() */

    },
    /* CBTest_displayElementThreeTimes() */



    /**
     * @return Promise -> object
     */
    CBTest_displayError:
    async function () {
        let errors = [
            Error("Error 1 | CBTest_displayError"),
            Error("Error 2 | CBTest_displayError"),
            Error("Error 3 | CBTest_displayError"),
        ];

        for (
            let index = 0;
            index < errors.length;
            index += 1
        ) {
            let error = errors[index];

            let controller = CBUIPanel.displayError2(
                error
            );

            await zeroTimeout();

            controller.CBUIPanel_close();
        }

        return {
            succeeded: true,
        };



        /**
         * @return Promise -> undefined
         */
        function zeroTimeout() {
            return new Promise(
                function (resolve) {
                    setTimeout(resolve, 1000);
                }
            );
        }
        /* zeroTimeout() */

    },
    /* CBTest_displayError() */



    /**
     * @return Promise -> object
     */
    CBTest_displayError_interactive:
    async function () {
        let errors = [
            Error("Error 1 | CBTest_displayError"),
            Error("Error 2 | CBTest_displayError"),
            Error("Error 3 | CBTest_displayError"),
        ];

        for (
            let index = 0;
            index < errors.length;
            index += 1
        ) {
            let error = errors[index];

            await CBUIPanel.displayError2(
                error
            ).CBUIPanel_getClosePromise();
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_displayError_interactive() */



    /**
     * @return object
     */
    CBTest_displayTextThreeTimes: function () {
        CBUIPanel.displayText(
            `
            He was so fluttered and so glowing with his good intentions, that
            his broken voice would scarcely answer to his call. He had been
            sobbing violently in his conflict with the Spirit, and his face was
            wet with tears.
            `
        );

        CBUIPanel.displayText(
            `
            “I will live in the Past, the Present, and the Future!”, Scrooge
            repeated, as he scrambled out of bed. “The Spirits of all Three
            shall strive within me. Oh Jacob Marley! Heaven, and the Christmas
            Time be praised for this! I say it on my knees, old Jacob; on my
            knees!”
            `
        );

        CBUIPanel.displayText(
            `
            And the bedpost was his own. The bed was his own, the room was his
            own. Best and happiest of all, the Time before him was his own, to
            make amends in!
            `
        );

        return {
            succeeded: true,
        };
    },
    /* CBTest_displayTextThreeTimes() */
};
/* CBUIPanel_Tests */

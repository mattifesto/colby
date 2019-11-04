"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBConvert,
    CBException,
    CBMessageMarkup,
    CBModel,
    CBTest,
    CBUI,
    CBUIExpander,
    CBUINavigationView,
    CBUIPanel,
    CBUISectionItem4,
    CBUISelector,
    CBUIStringsPart,
    Colby,

    CBTestAdmin,

    CBTestAdmin_tests,
*/

(function() {

    window.CBTestAdmin = {

        errorCount: 0,
        testImageID: "3dd8e721048bbe8ea5f0c043fab73277a0b0044c",



        /* -- functions -- -- -- -- -- */



        /**
         * @return object
         */
        createStatus() {
            var element = document.createElement("div");
            element.className = "status";

            return {
                element: element,
            };
        },



        /**
         * @param object test
         *
         *      {
         *          type: string
         *          testClassName: string
         *          name: string
         *      }
         *
         * @return function
         */
        convertJavaScriptTestToFunction(test) {
            let type = CBModel.valueToString(test, "type");

            if (type === "server" || type === "interactive_server") {
                let callable = CBModel.valueAsFunction(
                    CBTest,
                    "runServerTest"
                );

                if (callable) {
                    return callable;
                }
            }


            {
                let functionName =
                `CBTest_${test.name}`;

                let callable = CBModel.valueAsFunction(
                    window[test.testClassName],
                    functionName
                );

                if (callable) {
                    return callable;
                }
            }


            /**
             * @deprecated 2019_05_24
             */

            {
                let functionName =
                `CBTest_${test.name}`;

                let callable = CBModel.valueAsFunction(
                    window[test.testClassName + "Tests"],
                    functionName
                );

                if (callable) {
                    return callable;
                }
            }

            throw CBException.withError(
                Error(
                    `A JavaScript test function was not found for the ` +
                    `class name ${test.testClassName} and the test ` +
                    `name ${test.name}.`
                ),
                "",
                "5ac4cda27d2a6dfe61acf48bd51f8e0f8a7a959b"
            );
        },
        /* convertJavaScriptTestToFunction() */



        /**
         * @return Element
         */
        createTestUI() {
            var element = document.createElement("div");
            element.className = "CBTestUI";
            var containerElement = document.createElement("div");
            containerElement.className = "container";

            var img = document.createElement("img");
            img.src = "/colby/classes/CBTestAdmin/2017.02.02.TestImage.jpg";

            containerElement.appendChild(img);
            containerElement.appendChild(CBUI.createHalfSpace());

            var input = document.createElement("input");
            input.type = "file";
            input.style.display = "none";
            CBTestAdmin.fileInputElement = input;

            input.addEventListener(
                "change",
                function () {
                    CBTestAdmin.handleRunTests();
                }
            );

            containerElement.appendChild(input);

            {
                let sectionContainerElement = CBUI.createElement(
                    "CBUI_sectionContainer"
                );

                let sectionElement = CBUI.createElement(
                    "CBUI_section"
                );

                sectionContainerElement.appendChild(sectionElement);

                {
                    let options = [];

                    CBTestAdmin_tests.forEach(
                        function (test) {
                            let title = CBModel.valueToString(test, "title");
                            let type = CBModel.valueToString(test, "type");

                            if (type) {
                                title = `${title} (${type})`;
                            }

                            let description = CBModel.valueToString(
                                test,
                                "description"
                            );

                            options.push(
                                {
                                    title: title,
                                    description: description,
                                    value: test,
                                }
                            );
                        }
                    );
                    /* CBTestAdmin_tests.forEach */


                    options.sort(
                        function (a, b) {
                            return a.title.localeCompare(b.title);
                        }
                    );

                    options.unshift(
                        {
                            title: "All Tests",
                        }
                    );

                    let selector = CBUISelector.create();
                    selector.options = options;
                    selector.onchange = function () {
                        CBTestAdmin.selectedTest = selector.value;
                    };

                    sectionElement.appendChild(selector.element);
                }

                {
                    let runElement = CBUI.createElement(
                        "CBUI_action"
                    );

                    sectionElement.appendChild(runElement);

                    runElement.textContent = "Run";

                    runElement.addEventListener(
                        "click",
                        function () {
                            CBTestAdmin.status.element.textContent = "";

                            if (CBTestAdmin.selectedTest === undefined) {
                                input.click();
                            }

                            else if (
                                typeof CBTestAdmin.selectedTest === "object"
                            ) {
                                CBTestAdmin.runTest(CBTestAdmin.selectedTest);
                            }

                            else {
                                CBTestAdmin.handleRunTests();
                            }
                        }
                    );
                }

                containerElement.appendChild(sectionContainerElement);
            }


            let sectionElement = CBUI.createElement("CBUI_section");

            {
                let sectionContainerElement =
                CBUI.createElement("CBUI_sectionContainer");

                containerElement.appendChild(sectionContainerElement);
                sectionContainerElement.appendChild(sectionElement);
            }


            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    window.open('/colby/test-default-exception-handler/');
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = (
                    "Test the Colby Default Exception Handler"
                );

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);
            }

            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    window.open(
                        '/colby/test-cbhtmloutput-exception-handler/'
                    );
                };

                let stringsPart = CBUIStringsPart.create();

                stringsPart.string1 = (
                    "Test the CBHTMLOutput Custom Exception Handler"
                );

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);
            }

            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    throw new Error("Sample JavaScript Error");
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "JavaScript Error Test";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);
            }

            {
                let sectionItem = CBUISectionItem4.create();

                sectionItem.callback = function () {
                    Colby.callAjaxFunction(
                        "CBTestAdmin",
                        "testPagesRowAndDataStoreWithoutModel"
                    ).then(
                        function (ID) {
                            Colby.displayText(
                                `
                                    A row and data store with the ID "${ID}"
                                    were created and a CBPageVerificationTask
                                    was restarted.
                                `
                            );
                        }
                    ).catch(
                        function (error) {
                            CBUIPanel.displayError(error);
                            Colby.reportError(error);
                        }
                    );
                };

                let stringsPart = CBUIStringsPart.create();

                stringsPart.string1 = (
                    "Test Pages Row and Data Store Without Model"
                );

                stringsPart.string2 = `

                    This will create a pages row and data store but no model and
                    a CBPageVerificationTask which should remove the pages row
                    and data store and create a log entry saying so.

                `;

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);
            }

            var status = CBTestAdmin.createStatus();
            CBTestAdmin.status = status;

            element.appendChild(containerElement);
            element.appendChild(status.element);

            return element;
        },
        /* createTestUI() */



        /**
         * @return undefined
         */
        DOMContentDidLoad() {
            let main = document.getElementsByTagName("main")[0];
            let navigator = CBUINavigationView.create();

            main.appendChild(navigator.element);

            navigator.navigate(
                {
                    title: "Test",
                    element: CBTestAdmin.createTestUI(),
                }
            );
        },
        /* DOMContentDidLoad() */



        /**
         * @return undefined
         */
        handleRunTests() {
            CBTestAdmin.errorCount = 0;

            Promise.resolve().then(
                function (value) {
                    if (CBTestAdmin.selectedTest === undefined) {
                        return CBTestAdmin.runJavaScriptTests(value);
                    }
                }
            ).then(
                function () {
                    let expander = CBUIExpander.create();

                    if (CBTestAdmin.errorCount > 0) {
                        expander.severity = 3;
                        expander.title = (
                            `Finished running tests, ` +
                            `${CBTestAdmin.errorCount} failed`
                        );
                    } else {
                        expander.title = "All tests completed successfully";
                    }

                    CBTestAdmin.status.element.appendChild(expander.element);
                    expander.element.scrollIntoView();
                }
            ).catch(
                function (error) {
                    Colby.reportError(error);

                    let expander = CBUIExpander.create();

                    expander.severity = 3;
                    expander.title = error.message;

                    if (error.CBException) {
                        expander.message = error.CBException.extendedMessage;
                    }

                    CBTestAdmin.status.element.appendChild(expander.element);
                    expander.element.scrollIntoView();
                }
            ).then(
                function () {
                    CBTestAdmin.fileInputElementIsResetting = true;
                    CBTestAdmin.fileInputElement.value = null;
                    CBTestAdmin.fileInputElementIsResetting = undefined;
                }
            ).catch(
                function (error) {
                    CBUIPanel.displayError(error);
                    Colby.reportError(error);
                }
            );
        },
        /* handleRunTests() */



        /**
         * @return Promise
         *
         *      The promise returned by this function will not reject when an
         *      individual test fails because every test reports its own errors
         *      so that every test will run even if a test before it fails.
         */
        runJavaScriptTests() {
            let promise;

            CBTestAdmin_tests.forEach(
                function (currentTest) {
                    let type = CBModel.valueToString(currentTest, "type");

                    if (
                        type === "interactive" ||
                        type === "interactive_server"
                    ) {
                        return;
                    }

                    if (promise === undefined) {
                        promise = CBTestAdmin.runTest(currentTest);
                    } else {
                        promise = promise.then(
                            function () {
                                return CBTestAdmin.runTest(currentTest);
                            }
                        );
                    }
                }
            );

            return promise;
        },
        /* runJavaScriptTests() */



        /**
         * @param object test
         *
         *      {
         *          type: string
         *          testClassName: string
         *          name: string
         *      }
         *
         * @return Promise
         */
        runTest(test) {
            let expander;
            let title;

            let promise = new Promise(
                function (resolve) {
                    title = CBModel.valueToString(test, "title");

                    expander = CBUIExpander.create();
                    expander.title = title + " (running)";

                    CBTestAdmin.status.element.appendChild(expander.element);
                    expander.element.scrollIntoView();

                    let runTestNow =
                    CBTestAdmin.convertJavaScriptTestToFunction(test);

                    resolve(runTestNow);
                }
            ).then(
                function (runTestNow) {
                    let progress;

                    let args = {
                        set progress(value) {
                            progress = value;

                            let percent = (progress * 100).toFixed(1);

                            expander.title = title + ` (running ${percent}%)`;
                        },
                        get progress() {
                            return progress;
                        },
                        get test() {
                            return test;
                        },
                    };

                    return runTestNow(args);
                }
            ).then(
                function (value) {
                    return runTest_onFulfilled(value);
                }
            ).catch(
                function (error) {
                    return runTest_onRejected(error);
                }
            );

            return promise;



            /* -- closures -- -- -- -- -- */



            /**
             * @return undefined
             */
            function runTest_onFulfilled(value) {
                let message;
                let status;

                if (typeof value === "object") {
                    if (value.succeeded) {
                        status = "passed";
                    } else {
                        status = "failed";
                        CBTestAdmin.errorCount += 1;
                        expander.severity = 3;
                    }

                    message = value.message || status;
                } else {
                    CBTestAdmin.errorCount += 1;
                    expander.severity = 3;
                    message = `

                        This test failed because the test function did not
                        return an object.

                    `;
                }

                expander.title = `${title} (${status})`;
                expander.message = message;
            }
            /* runTest_onFulfilled() */



            /**
             * @return undefined
             */
            function runTest_onRejected(error) {
                let descriptionAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.errorToDescription(error)
                );
                let stackTraceAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.errorToStackTrace(error)
                );

                CBTestAdmin.errorCount += 1;
                expander.severity = 3;
                expander.title = `${title} failed`;
                expander.message = `

                    ${descriptionAsMessage}

                    --- pre\n${stackTraceAsMessage}
                    ---

                `;
            }
            /* runTest_onRejected() */

        },
        /* runTest() */

    };
    /* window.CBTestAdmin */

})();


Colby.afterDOMContentLoaded(
    function () {
        CBTestAdmin.DOMContentDidLoad();
    }
);

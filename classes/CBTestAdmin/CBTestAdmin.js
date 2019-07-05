"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTestAdmin */
/* global
    CBConvert,
    CBException,
    CBMessageMarkup,
    CBModel,
    CBTest,
    CBUI,
    CBUIExpander,
    CBUINavigationView,
    CBUISection,
    CBUISectionItem4,
    CBUISelector,
    CBUIStringsPart,
    Colby,

    CBTestAdmin_tests,
*/

var CBTestAdmin = {

    errorCount: 0,
    testImageID: "3dd8e721048bbe8ea5f0c043fab73277a0b0044c",

    createStatus: function () {
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
     *          testName: string
     *      }
     *
     * @return function
     */
    convertJavaScriptTestToFunction: function (test) {
        let type = CBModel.valueToString(test, "type");

        if (type === "server") {
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
            `CBTest_${test.testName}`;

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
                "deprecated style test function not found",
                "",
                "5ac4cda27d2a6dfe61acf48bd51f8e0f8a7a959b"
            )
        );
    },
    /* convertJavaScriptTestToFunction() */


    /**
     * @return Element
     */
    createTestUI: function () {
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

        input.addEventListener("change", CBTestAdmin.handleRunTests);

        containerElement.appendChild(input);

        {
            let section = CBUISection.create();

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


                CBTestAdmin.serverTests.forEach(function (serverTest, index) {
                    options.push({
                        title: `${serverTest[0]} / ${serverTest[1]}`,
                        value: index,
                    });
                });

                options.sort(function (a, b) {
                    return a.title.localeCompare(b.title);
                });

                options.unshift({
                    title: "All Tests",
                });

                let selector = CBUISelector.create();
                selector.options = options;
                selector.onchange = function () {
                    CBTestAdmin.selectedTest = selector.value;
                };

                section.appendItem(selector);
            }

            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    CBTestAdmin.status.element.textContent = "";

                    if (CBTestAdmin.selectedTest === undefined) {
                        input.click();
                    } else if (typeof CBTestAdmin.selectedTest === "object") {
                        CBTestAdmin.runTest(CBTestAdmin.selectedTest);
                    } else {
                        CBTestAdmin.handleRunTests();
                    }
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Run";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            containerElement.appendChild(section.element);
            containerElement.appendChild(CBUI.createHalfSpace());
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
            stringsPart.string1 = "Test the Colby Default Exception Handler";

            stringsPart.element.classList.add("action");

            sectionItem.appendPart(stringsPart);
            sectionElement.appendChild(sectionItem.element);
        }

        {
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                window.open('/colby/test-cbhtmloutput-exception-handler/');
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Test the CBHTMLOutput Custom Exception Handler";

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
                Colby.callAjaxFunction("CBTestAdmin", "testPagesRowAndDataStoreWithoutModel")
                    .then(function (ID) {
                        Colby.alert(`

                            A row and data store with the ID "${ID}" were
                            created and a CBPageVerificationTask was restarted.

                        `);
                    });
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Test Pages Row and Data Store Without Model";
            stringsPart.string2 = `

                This will create a pages row and data store but no model and a
                CBPageVerificationTask which should remove the pages row and
                data store and create a log entry saying so.

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
    DOMContentDidLoad: function() {
        Colby.fetchAjaxResponse(
            "/api/?class=CBUnitTests&function=getListOfTests"
        ).then(
            function (response) {
                CBTestAdmin.serverTests = response.tests;

                let main = document.getElementsByTagName("main")[0];
                let navigator = CBUINavigationView.create();

                main.appendChild(navigator.element);

                navigator.navigate({
                    title: "Test",
                    element: CBTestAdmin.createTestUI(),
                });
            }
        ).catch(
            function (error) {
                Colby.displayAndReportError(error);
            }
        );
    },
    /* DOMContentDidLoad() */


    /**
     * @return undefined
     */
    handleRunTests: function () {

        /**
         * IE11 incorrectly fires the changed event on the input element when
         * its value is reset to null when the tests are complete. This test
         * makes this function return early if it is called in this situation.
         */

        if (CBTestAdmin.fileInputElementIsResetting) {
            return;
        }

        CBTestAdmin.errorCount = 0;

        Promise.resolve().then(
            function (value) {
                if (CBTestAdmin.selectedTest === undefined) {
                    return CBTestAdmin.runJavaScriptTests(value);
                }
            }
        ).then(
            function (value) {
                return CBTestAdmin.runServerTests(value);
            }
        ).then(
            function (value) {
                return handleRunTests_onFulfilled(value);
            }
        ).catch(
            function (error) {
                return handleRunTests_onRejected(error);
            }
        ).then(
            function () {
                return handleRunTests_onFinally();
            }
        ).catch(
            function (error) {
                return Colby.displayAndReportError(error);
            }
        );

        return;


        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
        function handleRunTests_onFulfilled() {
            let expander = CBUIExpander.create();

            if (CBTestAdmin.errorCount > 0) {
                expander.severity = 3;
                expander.title = `Finished running tests, ${CBTestAdmin.errorCount} failed`;
            } else {
                expander.title = "All tests completed successfully";
            }

            CBTestAdmin.status.element.appendChild(expander.element);
            expander.element.scrollIntoView();
        }
        /* handleRunTests_onFulfilled() */


        /**
         * @return undefined
         */
        function handleRunTests_onRejected(error) {
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
        /* handleRunTests_onRejected() */


        /**
         * @return undefined
         */
        function handleRunTests_onFinally() {
            CBTestAdmin.fileInputElementIsResetting = true;
            CBTestAdmin.fileInputElement.value = null;
            CBTestAdmin.fileInputElementIsResetting = undefined;
        }
        /* handleRunTests_onFinally() */
    },
    /* handleRunTests() */


    /**
     * @return Promise
     *
     *      The promise returned by this function will not reject when an
     *      individual test fails because every test reports its own errors so
     *      that every test will run even if a test before it fails.
     */
    runJavaScriptTests: function () {
        let promise;

        CBTestAdmin_tests.forEach(
            function (currentTest) {
                let type = CBModel.valueToString(currentTest, "type");

                if (type === "interactive") {
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
     * @return Promise
     */
    runServerTests: function () {
        return new Promise(
            function (resolve, reject) {
                let i = 0;

                if (CBTestAdmin.selectedTest !== undefined) {
                    i = CBTestAdmin.selectedTest;
                }

                next();

                function run(test) {
                    var className = test[0];
                    var functionName = test[1];

                    let title = "The server test " +
                        (functionName ? `"${functionName}" ` : "") +
                        "for " +
                        className;
                    let expander = CBUIExpander.create();
                    expander.title = title + " (running)";

                    CBTestAdmin.status.element.appendChild(expander.element);
                    expander.element.scrollIntoView();

                    let args = {
                        className: className,
                        testName: functionName,
                    };

                    Colby.callAjaxFunction(
                        "CBTest",
                        "run",
                        args
                    ).then(
                        function (value) {
                            return onFulfilled(value);
                        }
                    ).catch(
                        function (error) {
                            return onRejected(error);
                        }
                    );

                    function onFulfilled(value) {
                        let status = value.succeeded ? "succeeded" : "failed";
                        let message = value.message || "";

                        expander.title = `${title} ${status}`;
                        expander.message = message;
                        expander.timestamp = Date.now() / 1000;

                        if (!value.succeeded) {
                            CBTestAdmin.errorCount += 1;
                            expander.severity = 3;
                        }

                        if (CBTestAdmin.selectedTest === undefined) {
                            next();
                        } else {
                            resolve();
                        }
                    }

                    function onRejected(error) {
                        CBTestAdmin.errorCount += 1;
                        expander.severity = 3;
                        expander.title = `${title} failed`;
                        expander.message = error.message;

                        reject(error);
                    }
                }

                /* closure */
                function next() {
                    if (i < CBTestAdmin.serverTests.length) {
                        run(CBTestAdmin.serverTests[i]);
                        i += 1;
                    } else {
                        resolve();
                    }
                }
            }
        );
        /* new Promise() */
    },

    /**
     * @param object test
     *
     *      {
     *          type: string
     *          testClassName: string
     *          testName: string
     *      }
     *
     * @return Promise
     */
    runTest: function (test) {
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
                message = "This test failed because the test function did not return an object.";
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
/* CBTestAdmin */


Colby.afterDOMContentLoaded(CBTestAdmin.DOMContentDidLoad);

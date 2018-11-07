"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTestAdmin */
/* global
    CBConvert,
    CBMessageMarkup,
    CBTestAdmin_javaScriptTests,
    CBUI,
    CBUIExpander,
    CBUINavigationView,
    CBUISection,
    CBUISectionItem4,
    CBUISelector,
    CBUIStringsPart,
    CBUI,
    Colby,
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
        let testObjectGlobalVariableName = `${test.testClassName}Tests`;
        let testObject = window[testObjectGlobalVariableName];

        if (typeof testObject !== "object") {
            let testFunction = function () {
                let message = `

                    The ${testObjectGlobalVariableName} global variable either
                    does not exist or is not an object.

                `;

                return {
                    succeeded: false,
                    message: message,
                };
            };

            return testFunction;
        } else {
            let testFunction = testObject[`CBTest_${test.testName}`];

            if (typeof testFunction !== "function") {
                testFunction = testObject[`${test.testName}Test`]; /* deprecated */

                if (typeof testFunction !== "function") {
                    testFunction = function () {
                        let message = `

                            No JavaScript function is available to run the
                            "${test.testName}" test for ${test.testClassName}.

                        `;

                        return {
                            succeeded: false,
                            message: message,
                        };
                    };
                }
            }

            return testFunction;
        }
    },

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

                CBTestAdmin.serverTests.forEach(function (serverTest, index) {
                    options.push({
                        title: `${serverTest[0]} / ${serverTest[1]}`,
                        value: index,
                    });
                });

                CBTestAdmin_javaScriptTests.forEach(function (test) {
                    options.push({
                        title: `${test.testClassName} / ${test.testName} (JavaScript)`,
                        value: test,
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

        let sectionElement = CBUI.createSection();

        {
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                window.open('/admin/?c=CBUnitTests&p=AdminPageException');
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Test CBPageSettingsForAdminPages::renderPageForException()";

            stringsPart.element.classList.add("action");

            sectionItem.appendPart(stringsPart);
            sectionElement.appendChild(sectionItem.element);
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
                Colby.callAjaxFunction("CBUnitTests", "errorTest")
                    .catch(Colby.displayAndReportError);
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Test the CBAjaxResponse Custom Exception Handler";

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

        containerElement.appendChild(sectionElement);
        containerElement.appendChild(CBUI.createHalfSpace());

        var status = CBTestAdmin.createStatus();
        CBTestAdmin.status = status;

        element.appendChild(containerElement);
        element.appendChild(status.element);

        return element;
    },

    /**
     * @return undefined
     */
    DOMContentDidLoad: function() {
        Colby.fetchAjaxResponse("/api/?class=CBUnitTests&function=getListOfTests")
            .then(function (response) {
                CBTestAdmin.serverTests = response.tests;

                let main = document.getElementsByTagName("main")[0];
                let navigator = CBUINavigationView.create();

                main.appendChild(navigator.element);

                navigator.navigate({
                    title: "Test",
                    element: CBTestAdmin.createTestUI(),
                });
            })
            .catch(Colby.displayAndReportError);
    },

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

        Promise.resolve()
            .then(function () {
                if (CBTestAdmin.selectedTest === undefined) {
                    return CBTestAdmin.runJavaScriptTests();
                }
            })
            .then(CBTestAdmin.runServerTests)
            .then(onFulfilled)
            .catch(onRejected)
            .then(onFinally, onFinally);

        function onFulfilled() {
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

        function onRejected(error) {
            Colby.reportError(error);
        }

        function onFinally() {
            CBTestAdmin.fileInputElementIsResetting = true;
            CBTestAdmin.fileInputElement.value = null;
            CBTestAdmin.fileInputElementIsResetting = undefined;
        }
    },

    /**
     * @return Promise
     *
     *      The promise returned by this function never rejects because every test
     *      will run even if it fails. (need research make sure this is right way of
     *      thinking)
     */
    runJavaScriptTests: function () {
        return new Promise(function (resolve, reject) {
            let index = 0;

            next();

            /* closure */
            function run() {
                let test = CBTestAdmin_javaScriptTests[index];

                CBTestAdmin.runTest(test).then(next);
            }

            /* closure */
            function next() {
                if (index < CBTestAdmin_javaScriptTests.length) {
                    run();
                    index += 1;
                } else {
                    resolve();
                }
            }
        });
    },

    /**
     * @return Promise
     */
    runServerTests: function () {
        return new Promise(function (resolve, reject) {
            let i = 0;

            if (CBTestAdmin.selectedTest !== undefined) {
                i = CBTestAdmin.selectedTest;
            }

            next();

            function run(test) {
                var className = test[0];
                var functionName = test[1];

                var URI = "/test/?class=" + className;

                if (functionName !== undefined) {
                    URI += "&function=" + functionName;
                }

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

                Colby.callAjaxFunction("CBTest", "run", args)
                    .then(onFulfilled)
                    .catch(onRejected);

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
        });
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
        let title = "JavaScript Test: " + test.testClassName + " - " + test.testName;
        let expander = CBUIExpander.create();
        expander.title = title + " (running)";

        CBTestAdmin.status.element.appendChild(expander.element);
        expander.element.scrollIntoView();

        let testFunction = CBTestAdmin.convertJavaScriptTestToFunction(test);

        return Promise.resolve(
            {
                set progress(value) {
                    let percent = (value * 100).toFixed(1);
                    expander.title = title + ` (running ${percent}%)`;
                }
            }
        ).then(
            testFunction
        ).then(
            onFulfilled
        ).catch(
            onRejected
        );

        /* closure */
        function onFulfilled(value) {
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

        /* closure */
        function onRejected(error) {
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
    },
};

Colby.afterDOMContentLoaded(CBTestAdmin.DOMContentDidLoad);

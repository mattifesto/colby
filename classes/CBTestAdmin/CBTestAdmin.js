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
    CBUISectionItem4,
    CBUIStringsPart,
    CBUI,
    Colby */

var CBTestAdmin = {

    testImageID: "3dd8e721048bbe8ea5f0c043fab73277a0b0044c",

    createStatus: function () {
        var element = document.createElement("div");
        element.className = "status";

        return {
            element: element,
        };
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

        let sectionElement = CBUI.createSection();

        {
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                input.click();
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Run Tests";

            stringsPart.element.classList.add("action");

            sectionItem.appendPart(stringsPart);
            sectionElement.appendChild(sectionItem.element);
        }

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
        var main = document.getElementsByTagName("main")[0];

        main.appendChild(CBTestAdmin.createTestUI());
    },

    /**
     * @return Promise
     */
    fetchServerTests: function () {
        return Colby.fetchAjaxResponse("/api/?class=CBUnitTests&function=getListOfTests");
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

        CBTestAdmin.status.element.textContent = "";

        Promise.resolve()
            .then(CBTestAdmin.runJavaScriptTests)
            .then(CBTestAdmin.fetchServerTests)
            .then(CBTestAdmin.runServerTests)
            .then(onFulfilled)
            .catch(onRejected)
            .then(onFinally, onFinally);

        function onFulfilled(errorCount) {
            let expander = CBUIExpander.create();

            if (errorCount > 0) {
                expander.severity = 3;
                expander.message = `Finished running tests, ${errorCount} failed`;
            } else {
                expander.message = "All tests completed successfully";
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
     */
    runJavaScriptTests: function () {
        return new Promise(function (resolve, reject) {
            var index = 0;

            next();

            /* closure */
            function run() {
                let test = CBTestAdmin_javaScriptTests[index];
                let className = test[0];
                let testClassName = `${className}Tests`;
                let testName = test[1];
                let testObject = window[testClassName];

                let title = "JavaScript Test: " + className + " - " + testName;
                let expander = CBUIExpander.create();
                expander.message = title + " (running)";

                CBTestAdmin.status.element.appendChild(expander.element);
                expander.element.scrollIntoView();

                let testFunction;

                if (typeof testObject !== "object") {
                    testFunction = function () {
                        let message = `The ${testClassName} object does not exist.`;

                        return {
                            succeeded: false,
                            message: message,
                        };
                    };
                } else {
                    testFunction = testObject[`CBTest_${testName}`];

                    if (typeof testFunction !== "function") {
                        testFunction = testObject[`${testName}Test`]; /* deprecated */

                        if (typeof testFunction !== "function") {
                            testFunction = function () {
                                let message = `

                                    No JavaScript function is available to run the
                                    "${testName}" test for ${className}.

                                `;

                                return {
                                    succeeded: false,
                                    message: message,
                                };
                            };
                        }
                    }
                }

                Promise.resolve()
                    .then(testFunction)
                    .then(onFulfilled)
                    .catch(onRejected)
                    .then(next);

                /* closure */
                function onFulfilled(value) {
                    let message;

                    if (typeof value === "object") {
                        if (value.succeeded) {
                            message = value.message || "succeeded";
                        } else {
                            // TODO: need to increase error count so test run doesn't succeed
                            expander.severity = 3;
                            message = value.message || "failed";
                        }
                    } else {
                        expander.severity = 3;
                        message = "This test failed because the test function did not return an object.";
                    }

                    expander.message = `

                        ${title}

                        ${message}

                    `;
                }

                /* closure */
                function onRejected(error) {
                    let descriptionAsMessage = CBMessageMarkup.stringToMessage(
                        CBConvert.errorToDescription(error)
                    );
                    let stackTraceAsMessage = CBMessageMarkup.stringToMessage(
                        CBConvert.errorToStackTrace(error)
                    );

                    expander.severity = 3;
                    expander.message = `

                        ${title} failed

                        ${descriptionAsMessage}

                        --- pre\n${stackTraceAsMessage}
                        ---

                    `;
                }
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
    runServerTests: function (fetchServerTestsResponse) {
        return new Promise(function (resolve, reject) {
            let i = 0;
            let errorCount = 0;

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
                expander.message = title + " (running)";

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
                    message = `${title} ${status}\n\n${message}`;

                    expander.message = message;
                    expander.severity = value.succeeded ? 6 : 3;
                    expander.timestamp = Date.now() / 1000;

                    if (!value.succeeded) {
                        errorCount += 1;
                    }

                    next();
                }

                function onRejected(error) {
                    expander.severity = 3;
                    expander.message = `

                        ${title} failed

                        ${error.message}

                    `;

                    reject(error);
                }
            }

            /* closure */
            function next() {
                if (i < fetchServerTestsResponse.tests.length) {
                    run(fetchServerTestsResponse.tests[i]);
                    i += 1;
                } else {
                    resolve(errorCount);
                }
            }
        });
    },

};

Colby.afterDOMContentLoaded(CBTestAdmin.DOMContentDidLoad);

"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTestAdmin */
/* global
    CBTestAdmin_javaScriptTests,
    CBUI,
    CBUISectionItem4,
    CBUIStringsPart,
    CBUI,
    Colby */

var CBTestAdmin = {

    testImageID: "3dd8e721048bbe8ea5f0c043fab73277a0b0044c",

    createStatus: function () {
        var element = document.createElement("div");
        element.className = "status";

        function append(message, className) {
            var lineElement = document.createElement("div");
            lineElement.className = "line";
            lineElement.textContent = message;

            if (className !== undefined) {
                try {
                    lineElement.classList.add(className);
                } catch (error) {
                    Colby.report(error);
                }
            }

            element.appendChild(lineElement);
        }

        function clear() {
            element.textContent = null;
        }

        return {
            append: append,
            clear: clear,
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
        var buttonsContainerElement = document.createElement("div");
        buttonsContainerElement.className = "buttonsContainer";

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
            let sectionElement = CBUI.createSection();
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                input.click();
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Run Tests";

            stringsPart.element.classList.add("action");

            sectionItem.appendPart(stringsPart);
            sectionElement.appendChild(sectionItem.element);
            containerElement.appendChild(sectionElement);
            containerElement.appendChild(CBUI.createHalfSpace());
        }

        var status = CBTestAdmin.createStatus();
        CBTestAdmin.status = status;

        buttonsContainerElement.appendChild(CBUI.createButton({
            callback: function () {
                window.open('/admin/?c=CBUnitTests&p=AdminPageException');
            },
            text: "Test CBPageSettingsForAdminPages::renderPageForException()",
        }).element);
        buttonsContainerElement.appendChild(CBUI.createButton({
            callback: function () {
                window.open('/colby/test-default-exception-handler/');
            },
            text: "Test the Colby Default Exception Handler",
        }).element);
        buttonsContainerElement.appendChild(CBUI.createButton({
            callback: function () {
                window.open('/colby/test-cbhtmloutput-exception-handler/');
            },
            text: "Test the CBHTMLOutput Custom Exception Handler",
        }).element);
        buttonsContainerElement.appendChild(CBUI.createButton({
            callback: function () {
                Colby.callAjaxFunction("CBUnitTests", "errorTest")
                    .catch(Colby.displayAndReportError);
            },
            text: "Test the CBAjaxResponse Custom Exception Handler",
        }).element);
        buttonsContainerElement.appendChild(CBUI.createButton({
            callback: function () {
                throw new Error("Sample JavaScript Error");
            },
            text: "JavaScript Error Test",
        }).element);

        containerElement.appendChild(buttonsContainerElement);
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

        var date = new Date();

        CBTestAdmin.status.clear();
        CBTestAdmin.status.append("Tests Started - " +
            date.toLocaleDateString() +
            " " +
            date.toLocaleTimeString());
        CBTestAdmin.status.append("\u00A0");

        Promise.resolve()
            .then(CBTestAdmin.runJavaScriptTests)
            .then(CBTestAdmin.fetchServerTests)
            .then(CBTestAdmin.runServerTests)
            .catch(report)
            .then(onFinally, onFinally);

        function report(error) {
            var message = "Failed: " + error.message;
            CBTestAdmin.status.append(message, "failure");
            Colby.reportError(error);

            Colby.alert(message);
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
                var test = CBTestAdmin_javaScriptTests[index];
                var className = test[0] + "Tests";
                var functionName = test[1] + "Test";
                var obj = window[className];

                CBTestAdmin.status.append("JavaScript Test: " +
                                                  className +
                                                  " - " +
                                                  functionName);

                if (typeof obj !== "object") {
                    throw new Error("The " + className + " object does not exist.");
                }

                var runTestFunction = obj[functionName];

                if (typeof runTestFunction !== "function") {
                    throw new Error("The " + functionName + " function does not exist.");
                }

                Promise.resolve()
                    .then(runTestFunction)
                    .then(onFulfilled)
                    .then(next)
                    .catch(reject);
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

            /* closure */
            function onFulfilled(value) {
                var message = "Succeeded";

                if (value && value.message) {
                    message = value.message;
                }

                CBTestAdmin.status.append(message, "success");
            }
        });
    },

    /**
     * @return Promise
     */
    runServerTests: function (fetchServerTestsResponse) {
        return new Promise(function (resolve, reject) {
            var i = 0;

            next();

            function run(test) {
                var className = test[0];
                var functionName = test[1];

                var URI = "/test/?class=" + className;

                if (functionName !== undefined) {
                    URI += "&function=" + functionName;
                }

                CBTestAdmin.status.append("Server Test: " + className + (functionName ? " - " + functionName : ''));

                Colby.fetchAjaxResponse(URI)
                    .then(reportTestSuccess)
                    .then(next)
                    .catch(reject);

                function reportTestSuccess(response) {
                    CBTestAdmin.status.append(response.message || "Succeeded", "success");
                }
            }

            /* closure */
            function next() {
                if (i < fetchServerTestsResponse.tests.length) {
                    run(fetchServerTestsResponse.tests[i]);
                    i += 1;
                } else {
                    resolve();
                }
            }
        });
    },

};

Colby.afterDOMContentLoaded(CBTestAdmin.DOMContentDidLoad);

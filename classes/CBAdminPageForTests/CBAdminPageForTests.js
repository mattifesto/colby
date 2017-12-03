"use strict";
/* jshint strict: global */
/* jshint esnext: true */
/* exported CBAdminPageForTests */
/* global
    CBAdminPageForTests_javaScriptTests,
    CBUI,
    Colby */

var CBAdminPageForTests = {

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
        img.src = "/colby/classes/CBAdminPageForTests/2017.02.02.TestImage.jpg";
        var input = document.createElement("input");
        input.type = "file";
        input.style.display = "none";

        var button = CBUI.createButton({
            callback: input.click.bind(input),
            text: "Run Tests",
        });

        var status = CBAdminPageForTests.createStatus();

        CBAdminPageForTests.status = status;

        CBAdminPageForTests.fileInputElement = input;

        input.addEventListener("change", CBAdminPageForTests.handleRunTests.bind(undefined, {
            button: button,
        }));

        buttonsContainerElement.appendChild(button.element);
        buttonsContainerElement.appendChild(CBUI.createButton({
            callback: function () {
                window.open('/admin/?c=CBUnitTests&p=AdminPageException');
            },
            text: "Admin Page Exception Test",
        }).element);
        buttonsContainerElement.appendChild(CBUI.createButton({
            callback: function () {
                window.open('/colby/test-default-exception-handler/');
            },
            text: "Test Default Exception Handler",
        }).element);
        buttonsContainerElement.appendChild(CBUI.createButton({
            callback: function () {
                Colby.callAjaxFunction("CBUnitTests", "errorTest")
                    .catch(Colby.displayAndReportError);
            },
            text: "Call Ajax Function PHP Error Test",
        }).element);
        buttonsContainerElement.appendChild(CBUI.createButton({
            callback: function () {
                throw new Error("Sample JavaScript Error");
            },
            text: "JavaScript Error Test",
        }).element);

        containerElement.appendChild(input);
        containerElement.appendChild(img);
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

        main.appendChild(CBAdminPageForTests.createTestUI());
    },

    /**
     * @return Promise
     */
    fetchServerTests: function () {
        return Colby.fetchAjaxResponse("/api/?class=CBUnitTests&function=getListOfTests");
    },

    /**
     * @param object args.button
     *
     * @return undefined
     */
    handleRunTests: function (args) {

        /**
         * IE11 incorrectly fires the changed event on the input element when
         * its value is reset to null when the tests are complete. This test
         * makes this function return early if it is called in this situation.
         */

        if (CBAdminPageForTests.fileInputElementIsResetting) {
            return;
        }

        var date = new Date();
        args.button.disable();

        CBAdminPageForTests.status.clear();
        CBAdminPageForTests.status.append("Tests Started - " +
            date.toLocaleDateString() +
            " " +
            date.toLocaleTimeString());
        CBAdminPageForTests.status.append("\u00A0");

        Promise.resolve()
            .then(CBAdminPageForTests.runJavaScriptTests)
            .then(CBAdminPageForTests.fetchServerTests)
            .then(CBAdminPageForTests.runServerTests)
            .catch(report)
            .then(onFinally, onFinally);

        function report(error) {
            var message = "Failed: " + error.message;
            CBAdminPageForTests.status.append(message, "failure");
            Colby.reportError(error);

            Colby.alert(message);
        }

        function onFinally() {
            args.button.enable();
            CBAdminPageForTests.fileInputElementIsResetting = true;
            CBAdminPageForTests.fileInputElement.value = null;
            CBAdminPageForTests.fileInputElementIsResetting = undefined;
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
                var test = CBAdminPageForTests_javaScriptTests[index];
                var className = test[0] + "Tests";
                var functionName = test[1] + "Test";
                var obj = window[className];

                CBAdminPageForTests.status.append("JavaScript Test: " +
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
                if (index < CBAdminPageForTests_javaScriptTests.length) {
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

                CBAdminPageForTests.status.append(message, "success");
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

                CBAdminPageForTests.status.append("Server Test: " + className + (functionName ? " - " + functionName : ''));

                Colby.fetchAjaxResponse(URI)
                    .then(reportTestSuccess)
                    .then(next)
                    .catch(reject);

                function reportTestSuccess(response) {
                    CBAdminPageForTests.status.append(response.message || "Succeeded", "success");
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

Colby.afterDOMContentLoaded(CBAdminPageForTests.DOMContentDidLoad);

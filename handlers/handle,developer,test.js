"use strict"; /* jshint strict: global */
/* globals Colby, ColbyUnitTests */

var CBTestPage = {

    /**
     * @param Element state.statusElement
     * @param string message
     * @param string? args.className
     *
     * @return undefined
     */
    appendStatus : function (state, message, args) {
        var lineElement = document.createElement("div");
        lineElement.className = "line";
        lineElement.textContent = message;

        if (args && args.className) {
            lineElement.classList.add(args.className);
        }

        state.statusElement.appendChild(lineElement);
    },

    /**
     * @param Element args.statusElement
     *
     * @return undefined
     */
    clearStatus : function (args) {
        args.statusElement.textContent = null;
    },

    /**
     * @return {Element}
     */
    createTestUI : function() {
        var element = document.createElement("div");
        element.className = "CBTestUI";
        var containerElement = document.createElement("div");
        containerElement.className = "container";
        var button = document.createElement("button");
        button.textContent = "Run Tests";
        var status = document.createElement("div");
        status.className = "status";

        var appendStatusCallback = CBTestPage.appendStatus.bind(undefined, {
            statusElement : status,
        });

        var clearStatusCallback = CBTestPage.clearStatus.bind(undefined, {
            statusElement : status,
        });

        button.addEventListener("click", CBTestPage.handleRunTests.bind(undefined, {
            appendStatusCallback : appendStatusCallback,
            buttonElement : button,
            clearStatusCallback : clearStatusCallback,
        }));

        containerElement.appendChild(button);
        element.appendChild(containerElement);
        element.appendChild(status);

        return element;
    },

    /**
    @return {undefined}
    */
    DOMContentDidLoad : function() {
        var main = document.getElementsByTagName("main")[0];

        main.appendChild(CBTestPage.createTestUI());
    },

    /**
     * @param function args.appendStatusCallback
     * @param element args.buttonElement
     *
     * @return undefined
     */
    handleListOfTestsReceived : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            CBTestPage.runTest({
                appendStatusCallback : args.appendStatusCallback,
                buttonElement   : args.buttonElement,
                index           : 0,
                tests           : response.tests,
            });
        } else {
            args.appendStatusCallback(response.message, {className:"error"});
            args.buttonElement.disabled = false;
        }
    },

    /**
     * @param function args.appendStatusCallback
     * @param Element args.buttonElement
     * @param function args.clearStatusCallback
     *
     * @return undefined
     */
    handleRunTests : function(args) {
        var date = new Date();
        args.buttonElement.disabled = true;
        var xhr = new XMLHttpRequest();
        xhr.onload = CBTestPage.handleListOfTestsReceived.bind(undefined, {
            buttonElement : args.buttonElement,
            appendStatusCallback : args.appendStatusCallback,
            xhr : xhr,
        });

        args.clearStatusCallback();
        args.appendStatusCallback("Tests Started - " +
            date.toLocaleDateString() +
            " " +
            date.toLocaleTimeString());
        args.appendStatusCallback("\u00A0");

        CBTestPage.runJavaScriptTests({
            appendStatusCallback : args.appendStatusCallback,
        });

        xhr.open('POST', '/api/?class=CBUnitTests&function=getListOfTests', true);
        xhr.send();
    },

    /**
     * @param function args.appendStatusCallback
     * @param Element args.buttonElement
     * @param int args.index
     * @param array args.tests
     * @param XMLHttpRequest args.xhr

     * @return undefined
     */
    handleTestCompleted : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);
        var message;
        args.index = args.index + 1;

        if (response.wasSuccessful) {
            message = response.message ? response.message : "Succeeded";
            args.appendStatusCallback(message, { className : "success" });
        } else {
            message = response.message ? response.message : "Failed";
            args.appendStatusCallback(message, { className : "failure" });
        }


        if (args.index < args.tests.length) {
            CBTestPage.runTest({
                appendStatusCallback : args.appendStatusCallback,
                buttonElement   : args.buttonElement,
                index           : args.index,
                tests           : args.tests,
            });
        } else {
            args.buttonElement.disabled = false;
        }
    },

    /**
     * @param function args.appendStatusCallback
     *
     * @return undefined
     */
    runJavaScriptTests : function(args) {
        args.appendStatusCallback("Starting JavaScript tests.");

        var message = ColbyUnitTests.runJavaScriptTests();

        args.appendStatusCallback(message, { className : "success" });
    },

    /**
     * @param function args.appendStatusCallback
     * @param Element args.buttonElement
     * @param int args.index
     * @param [object] args.tests
     *
     * @return undefined
     */
    runTest : function(args) {
        var className = args.tests[args.index][0];
        var functionName = args.tests[args.index][1];

        var xhr = new XMLHttpRequest();
        xhr.onload = CBTestPage.handleTestCompleted.bind(undefined, {
            appendStatusCallback : args.appendStatusCallback,
            buttonElement : args.buttonElement,
            index : args.index,
            tests : args.tests,
            xhr : xhr,
        });

        var URI = "/test/?class=" + className;

        if (functionName !== undefined) {
            URI += "&function=" + functionName;
        }

        xhr.open('POST', URI, true);
        xhr.send();

        args.appendStatusCallback("Test: " + className + (functionName ? " - " + functionName : ''));
    },
};

document.addEventListener("DOMContentLoaded", CBTestPage.DOMContentDidLoad);

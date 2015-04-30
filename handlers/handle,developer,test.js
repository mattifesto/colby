"use strict";

var CBTestPage = {

    /**
    @return {Element}
    */
    createTestUI : function() {
        var element         = document.createElement("div");
        element.className   = "CBTestUI";
        var button          = document.createElement("button");
        button.textContent  = "Run Tests";
        var status          = document.createElement("textarea");

        button.addEventListener("click", CBTestPage.handleRunTests.bind(undefined, {
            buttonElement   : button,
            statusElement   : status }));

        element.appendChild(button);
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
    @return {undefined}
    */
    handleListOfTestsReceived : function(args) {
        var response    = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            CBTestPage.runTest({
                buttonElement   : args.buttonElement,
                index           : 0,
                statusElement   : args.statusElement,
                tests           : response.tests });
        } else {
            args.statusElement.value += response.message + "\n";
            args.buttonElement.disabled = false;
        }
    },

    /**
    @param {Element}    buttonElement
    @param {Element}    statusElement

    @return {undefined}
    */
    handleRunTests : function(args) {
        var date                    = new Date();
        args.buttonElement.disabled = true;
        var xhr                     = new XMLHttpRequest();
        xhr.onload                  = CBTestPage.handleListOfTestsReceived.bind(undefined, {
            buttonElement           : args.buttonElement,
            statusElement           : args.statusElement,
            xhr                     : xhr });

        args.statusElement.value    = "Tests Started - " +
                                      date.toLocaleDateString() +
                                      " " +
                                      date.toLocaleTimeString() +
                                      "\n";

        CBTestPage.runJavaScriptTests({
            statusElement   : args.statusElement });

        xhr.open('POST', '/api/?class=CBUnitTests&function=getListOfTests', true);
        xhr.send();
    },

    /**
    @param {Element}        buttonElement
    @param {int}            index
    @param {Element}        statusElement
    @param {array}          tests
    @param {XMLHttpRequest} xhr

    @return {undefined}
    */
    handleTestCompleted : function(args) {
        var response    = Colby.responseFromXMLHttpRequest(args.xhr);
        args.index      = args.index + 1;

        if (response.wasSuccessful && !response.message) {
            response.message = "Succeeded";
        }

        args.statusElement.value += response.message + "\n";

        if (args.index < args.tests.length) {
            CBTestPage.runTest({
                buttonElement   : args.buttonElement,
                index           : args.index,
                statusElement   : args.statusElement,
                tests           : args.tests });
        } else {
            args.buttonElement.disabled = false;
        }
    },

    /**
    @param {Element} statusElement

    @return void
    */
    runJavaScriptTests : function(args) {
        args.statusElement.value += "Starting JavaScript tests.\n";

        var message = ColbyUnitTests.runJavaScriptTests();

        args.statusElement.value += message + "\n";
    },

    /**
    @param {Element}    buttonElement
    @param {int}        index
    @param {Element}    statusElement
    @param {array}      tests

    @return {undefined}
    */
    runTest : function(args) {
        var className       = args.tests[args.index][0];
        var functionName    = args.tests[args.index][1];
        var xhr             = new XMLHttpRequest();
        xhr.onload          = CBTestPage.handleTestCompleted.bind(undefined, {
            buttonElement   : args.buttonElement,
            index           : args.index,
            statusElement   : args.statusElement,
            tests           : args.tests,
            xhr             : xhr });
        var URI             = "/test/?class=" + className;

        if (functionName !== undefined) {
            URI += "&function=" + functionName;
        }

        xhr.open('POST', URI, true);
        xhr.send();

        args.statusElement.value += "Test: " + className + (functionName ? " - " + functionName : '') + "\n";
    }
};

document.addEventListener("DOMContentLoaded", CBTestPage.DOMContentDidLoad);

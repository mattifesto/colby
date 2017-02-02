"use strict"; /* jshint strict: global */
/* globals
    Colby */

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

var ColbyUnitTests = {

    /**
     * @return string
     */
    runJavaScriptTests : function() {
        var countOfTests;
        var i;
        var wasSuccessful = true;
        var now = new Date('2012/12/16 10:51 pm');
        var tests = [];

        ColbyUnitTests.errors = '';

        // Setup tests

        tests.push({
            'date' : now,
            'string' : '0 seconds ago'
        });

        tests.push({
            'date' : new Date(now.getTime() - 1000),
            'string' : '1 seconds ago'
        });

        tests.push({
            'date' : new Date(now.getTime() - (1000 * 59)),
            'string' : '59 seconds ago'
        });

        tests.push({
            'date' : new Date(now.getTime() - (1000 * 60)),
            'string' : '1 minute ago'
        });

        tests.push({
            'date' : new Date(now.getTime() - (1000 * 60 * 2)),
            'string' : '2 minutes ago'
        });

        tests.push({
            'date' : new Date(now.getTime() - (1000 * 60 * 59)),
            'string' : '59 minutes ago'
        });

        tests.push({
            'date' : new Date('2012/12/16 9:51 pm'),
            'string' : 'Today at 9:51 p.m.'
        });

        tests.push({
            'date' : new Date('2012/12/16 12:00 am'),
            'string' : 'Today at 12:00 a.m.'
        });

        tests.push({
            'date' : new Date('2012/12/15 11:59 pm'),
            'string' : 'Yesterday at 11:59 p.m.'
        });

        tests.push({
            'date' : new Date('2012/12/15 12:00 am'),
            'string' : 'Yesterday at 12:00 a.m.'
        });

        tests.push({
            'date' : new Date('2012/12/14 1:53 pm'),
            'string' : 'December 14, 2012 1:53 p.m.'
        });

        // Run tests

        countOfTests = tests.length;

        for (i = 0; i < countOfTests; i++)
        {
            var string = Colby.dateToRelativeLocaleString(tests[i].date, now);

            if (string != tests[i].string)
            {
                ColbyUnitTests.errors +=
                    'test failed\nexpected: "' +
                    tests[i].string +
                    '"\nreceived: "' +
                    string +
                    '"';

                wasSuccessful = false;

                break;
            }
        }

        /**
         * Test `Colby.centsToDollars`
         */

        tests = [];

        tests.push({
            "input" : 0,
            "expected" : "0.00"
        });

        tests.push({
            "input" : "020",
            "expected" : "0.20"
        });

        tests.push({
            "input" : "10",
            "expected" : "0.10"
        });

        tests.push({
            "input" : 110,
            "expected" : "1.10"
        });

        tests.push({
            "input" : 3234393,
            "expected" : "32343.93"
        });

        countOfTests = tests.length;

        for (i = 0; i < countOfTests; i++)
        {
            var output = Colby.centsToDollars(tests[i].input);

            if (output != tests[i].expected)
            {
                ColbyUnitTests.errors += "<div>`Colby.centsToDollars` test failed" +
                                         "<p>input: " + tests[i].input +
                                         "<p>output: " + output +
                                         "<p>expected: " + tests[i].expected +
                                         "</div>";

                wasSuccessful = false;

                break;
            }
        }

        /**
         * Report results
         */

        if (wasSuccessful)
        {
            return "Javascript unit tests passed.";
        }
        else
        {
            return "Javascript unit tests failed.\n\n" + ColbyUnitTests.errors;
        }
    }
};

document.addEventListener("DOMContentLoaded", CBTestPage.DOMContentDidLoad);

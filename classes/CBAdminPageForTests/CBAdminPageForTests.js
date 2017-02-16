"use strict"; /* jshint strict: global */
/* jshint esnext: true */
/* globals
    Colby */

var CBAdminPageForTests = {

    testImageID: "3dd8e721048bbe8ea5f0c043fab73277a0b0044c",
    fileInputElement: undefined,
    promise: undefined,

    /**
     * @param string URI
     *
     * @return Promise
     */
    fetchURIDoesExist: function (URI) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.onloadend = handler;
            xhr.open("HEAD", URI);
            xhr.send();

            function handler() {
                if (xhr.status === 200) {
                    resolve(true);
                } else if (xhr.status === 404) {
                    resolve(false); // The image has been deleted, as expected.
                } else {
                    reject(new Error("verifyURIExistence() request returned an unexpected status: " + xhr.status));
                }
            }
        });
    },

    /**
     * @param mixed value
     *      This function is meant to be a parameter to Promise.then().
     *
     * @return Promise
     */
    runTestForClassCBImagesFunctionDeleteByID: function (value) {
        CBAdminPageForTests.appendStatusCallback("Ajax: CBImages - deleteByID");

        var URL = "/api/?class=CBImages&function=deleteByID";
        var data = new FormData();

        data.append("ID", CBAdminPageForTests.testImageID);

        var imageURI = "/" + Colby.dataStoreFlexpath(CBAdminPageForTests.testImageID, "original.jpeg");

        return Colby.fetchAjaxResponse(URL, data)
                    .then(report1)
                    .then(report2);

        function report1(response) {
            CBAdminPageForTests.appendStatusCallback(response.message, { className : "success" });
            CBAdminPageForTests.appendStatusCallback("Ajax: CBImages - check for original image file");

            return CBAdminPageForTests.fetchURIDoesExist(imageURI);
        }

        function report2(doesExist) {
            if (doesExist) {
                throw new Error("The image file is available.");
            } else {
                CBAdminPageForTests.appendStatusCallback("The image file is not available.", { className : "success" });
            }
        }
    },

    /**
     * @param mixed value
     *      This function is meant to be a parameter to Promise.then().
     *
     * @return Promise
     */
    runTestForClassCBImagesFunctionUpload: function (value) {
        CBAdminPageForTests.appendStatusCallback("Ajax: CBImages - upload");

        var URL = "/api/?class=CBImages&function=upload";
        var data = new FormData();

        data.append("image", CBAdminPageForTests.fileInputElement.files[0]);

        return Colby.fetchAjaxResponse(URL, data)
                    .then(report1)
                    .then(report2)
                    .then(report3);

        function report1(response) {
            var image = response.image;

            if (image.extension === "jpeg" &&
                image.filename === "original" &&
                image.height === 900 &&
                image.ID === CBAdminPageForTests.testImageID &&
                image.width === 1600)
            {
                CBAdminPageForTests.appendStatusCallback(response.message, { className : "success" });
                CBAdminPageForTests.appendStatusCallback("Ajax: CBImages - check for original image file");

                var imageURI = "/" + Colby.dataStoreFlexpath(CBAdminPageForTests.testImageID, "original.jpeg");

                return CBAdminPageForTests.fetchURIDoesExist(imageURI);
            } else {
                throw new Error("The image file did not upload correctly.");
            }
        }

        function report2(doesExist) {
            if (doesExist) {
                CBAdminPageForTests.appendStatusCallback("The image file is available.", { className : "success" });
                CBAdminPageForTests.appendStatusCallback("Ajax: CBImages - check for resized image file");

                var imageURI = "/" + Colby.dataStoreFlexpath(CBAdminPageForTests.testImageID, "rw640.jpeg");

                return CBAdminPageForTests.fetchURIDoesExist(imageURI);
            } else {
                throw new Error("The image file is not available.");
            }
        }

        function report3(doesExist) {
            if (doesExist) {
                CBAdminPageForTests.appendStatusCallback("The image file is available.", { className : "success" });
            } else {
                throw new Error("The image file is not available.");
            }
        }
    },
};

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
     * @return Element
     */
    createTestUI : function () {
        var element = document.createElement("div");
        element.className = "CBTestUI";
        var containerElement = document.createElement("div");
        containerElement.className = "container";
        var button = document.createElement("button");
        button.textContent = "Run Tests";
        var img = document.createElement("img");
        img.src = "/colby/classes/CBAdminPageForTests/2017.02.02.TestImage.jpg";
        var input = document.createElement("input");
        input.type = "file";
        input.style.display = "none";
        var status = document.createElement("div");
        status.className = "status";

        CBAdminPageForTests.fileInputElement = input;

        CBAdminPageForTests.appendStatusCallback = CBTestPage.appendStatus.bind(undefined, {
            statusElement : status,
        });

        CBAdminPageForTests.clearStatusCallback = CBTestPage.clearStatus.bind(undefined, {
            statusElement : status,
        });

        button.addEventListener("click", input.click.bind(input));

        input.addEventListener("change", CBTestPage.handleRunTests.bind(undefined, {
            buttonElement : button,
        }));

        containerElement.appendChild(input);
        containerElement.appendChild(img);
        containerElement.appendChild(button);
        element.appendChild(containerElement);
        element.appendChild(status);

        return element;
    },

    /**
     * @return undefined
     */
    DOMContentDidLoad : function() {
        var main = document.getElementsByTagName("main")[0];

        main.appendChild(CBTestPage.createTestUI());
    },

    /**
     * @param element args.buttonElement
     *
     * @return undefined
     */
    handleListOfTestsReceived : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            CBTestPage.runTest({
                buttonElement   : args.buttonElement,
                index           : 0,
                tests           : response.tests,
            });
        } else {
            CBAdminPageForTests.appendStatusCallback(response.message, {className:"error"});
            args.buttonElement.disabled = false;
        }
    },

    /**
     * @param Element args.buttonElement
     *
     * @return undefined
     */
    handleRunTests: function (args) {
        var date = new Date();
        args.buttonElement.disabled = true;

        CBAdminPageForTests.clearStatusCallback();
        CBAdminPageForTests.appendStatusCallback("Tests Started - " +
            date.toLocaleDateString() +
            " " +
            date.toLocaleTimeString());
        CBAdminPageForTests.appendStatusCallback("\u00A0");

        CBAdminPageForTests.promise = Promise.resolve()
            .then(CBTestPage.runJavaScriptTests)
            .then(CBAdminPageForTests.runTestForClassCBImagesFunctionDeleteByID)
            .then(CBAdminPageForTests.runTestForClassCBImagesFunctionUpload)
            .then(resolved, rejected);

        function resolved() {
            CBAdminPageForTests.promise = undefined;

            var xhr = new XMLHttpRequest();
            xhr.onload = CBTestPage.handleListOfTestsReceived.bind(undefined, {
                buttonElement : args.buttonElement,
                xhr : xhr,
            });

            xhr.open('POST', '/api/?class=CBUnitTests&function=getListOfTests', true);
            xhr.send();
        }

        function rejected(error) {
            CBAdminPageForTests.appendStatusCallback("Failed: " + error.message, {className:"failure"});
        }
    },

    /**
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
            CBAdminPageForTests.appendStatusCallback(message, { className : "success" });
        } else {
            message = response.message ? response.message : "Failed";
            CBAdminPageForTests.appendStatusCallback(message, { className : "failure" });
        }


        if (args.index < args.tests.length) {
            CBTestPage.runTest({
                buttonElement   : args.buttonElement,
                index           : args.index,
                tests           : args.tests,
            });
        } else {
            args.buttonElement.disabled = false;
        }
    },

    /**
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

        CBAdminPageForTests.appendStatusCallback("Test: " + className + (functionName ? " - " + functionName : ''));
    },

    /**
     * @param mixed value
     *  This function is meant to be used in a then() of a promise.
     *
     * @return undefined
     */
    runJavaScriptTests: function(value) {
        CBAdminPageForTests.appendStatusCallback("Starting synchronous JavaScript tests.");

        var countOfTests;
        var i;
        var wasSuccessful = true;
        var now = new Date('2012/12/16 10:51 pm');
        var tests = [];

        CBTestPage.errors = '';

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

        for (i = 0; i < countOfTests; i++) {
            var string = Colby.dateToRelativeLocaleString(tests[i].date, now);

            if (string != tests[i].string) {
                CBTestPage.errors +=
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

        for (i = 0; i < countOfTests; i++) {
            var output = Colby.centsToDollars(tests[i].input);

            if (output != tests[i].expected) {
                CBTestPage.errors += "`Colby.centsToDollars` test failed\n" +
                                     "input: " + tests[i].input + "\n" +
                                     "output: " + output + "\n" +
                                     "expected: " + tests[i].expected + "\n";

                wasSuccessful = false;

                break;
            }
        }

        /**
         * Report results
         */

        if (wasSuccessful) {
            var message = "Succeeded";
            CBAdminPageForTests.appendStatusCallback(message, {className:"success"});
        } else {
            throw new Error("Javascript unit tests failed.\n\n" + CBTestPage.errors);
        }
    }
};

document.addEventListener("DOMContentLoaded", CBTestPage.DOMContentDidLoad);

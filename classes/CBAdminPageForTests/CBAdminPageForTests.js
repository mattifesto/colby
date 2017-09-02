"use strict"; /* jshint strict: global */
/* jshint esnext: true */
/* globals
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
        CBAdminPageForTests.status.append("Ajax: CBImages - deleteByID");

        var URL = "/api/?class=CBImages&function=deleteByID";
        var data = new FormData();

        data.append("ID", CBAdminPageForTests.testImageID);

        var imageURI = "/" + Colby.dataStoreFlexpath(CBAdminPageForTests.testImageID, "original.jpeg");

        return Colby.fetchAjaxResponse(URL, data)
                    .then(report1)
                    .then(report2);

        function report1(response) {
            CBAdminPageForTests.status.append(response.message, "success");
            CBAdminPageForTests.status.append("Ajax: CBImages - check for original image file");

            return CBAdminPageForTests.fetchURIDoesExist(imageURI);
        }

        function report2(doesExist) {
            if (doesExist) {
                throw new Error("The image file is available.");
            } else {
                CBAdminPageForTests.status.append("The image file is not available.", "success");
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
        CBAdminPageForTests.status.append("Ajax: CBImages - upload");

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
                CBAdminPageForTests.status.append(response.message, "success");
                CBAdminPageForTests.status.append("Ajax: CBImages - check for original image file");

                var imageURI = "/" + Colby.dataStoreFlexpath(CBAdminPageForTests.testImageID, "original.jpeg");

                return CBAdminPageForTests.fetchURIDoesExist(imageURI);
            } else {
                throw new Error("The image file did not upload correctly.");
            }
        }

        function report2(doesExist) {
            if (doesExist) {
                CBAdminPageForTests.status.append("The image file is available.", "success");
                CBAdminPageForTests.status.append("Ajax: CBImages - check for resized image file");

                var imageURI = "/" + Colby.dataStoreFlexpath(CBAdminPageForTests.testImageID, "rw640.jpeg");

                return CBAdminPageForTests.fetchURIDoesExist(imageURI);
            } else {
                throw new Error("The image file is not available.");
            }
        }

        function report3(doesExist) {
            if (doesExist) {
                CBAdminPageForTests.status.append("The image file is available.", "success");
            } else {
                throw new Error("The image file is not available.");
            }
        }
    },
};

var CBTestPage = {

    /**
     * @return Element
     */
    createTestUI: function () {
        var element = document.createElement("div");
        element.className = "CBTestUI";
        var containerElement = document.createElement("div");
        containerElement.className = "container";
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

        input.addEventListener("change", CBTestPage.handleRunTests.bind(undefined, {
            button: button,
        }));

        containerElement.appendChild(input);
        containerElement.appendChild(img);
        containerElement.appendChild(button.element);
        element.appendChild(containerElement);
        element.appendChild(status.element);

        return element;
    },

    /**
     * @return undefined
     */
    DOMContentDidLoad: function() {
        var main = document.getElementsByTagName("main")[0];

        main.appendChild(CBTestPage.createTestUI());
    },

    /**
     * @param object args.button
     *
     * @return undefined
     */
    handleRunTests: function (args) {
        var date = new Date();
        args.button.disable();

        CBAdminPageForTests.status.clear();
        CBAdminPageForTests.status.append("Tests Started - " +
            date.toLocaleDateString() +
            " " +
            date.toLocaleTimeString());
        CBAdminPageForTests.status.append("\u00A0");

        Promise.resolve()
            .then(CBTestPage.runJavaScriptTests)
            .then(CBAdminPageForTests.runTestForClassCBImagesFunctionDeleteByID)
            .then(CBAdminPageForTests.runTestForClassCBImagesFunctionUpload)
            .then(fetchTests)
            .then(runTests)
            .catch(report)
            .then(onFinally, onFinally);

        function fetchTests() {
            return Colby.fetchAjaxResponse("/api/?class=CBUnitTests&function=getListOfTests");
        }

        function runTests(response) {
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

                    CBAdminPageForTests.status.append("Test: " + className + (functionName ? " - " + functionName : ''));

                    Colby.fetchAjaxResponse(URI)
                        .then(reportTestSuccess, reportTestFailure)
                        .then(next, next);

                    function reportTestSuccess(response) {
                        CBAdminPageForTests.status.append(response.message || "Succeeded", "success");
                    }

                    function reportTestFailure(error) {
                        CBAdminPageForTests.status.append(error.message || "Failed", "failure");
                    }
                }

                function next() {
                    if (i < response.tests.length) {
                        run(response.tests[i]);
                        i += 1;
                    } else {
                        resolve();
                    }
                }
            });
        }

        function report(error) {
            CBAdminPageForTests.status.append("Failed: " + error.message, "failure");
        }

        function onFinally() {
            args.button.enable();
            CBAdminPageForTests.fileInputElement.value = null;
        }
    },

    /**
     * @param mixed value
     *  This function is meant to be used in a then() of a promise.
     *
     * @return undefined
     */
    runJavaScriptTests: function(value) {
        CBAdminPageForTests.status.append("Starting synchronous JavaScript tests.");

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
            CBAdminPageForTests.status.append(message, "success");
        } else {
            throw new Error("Javascript unit tests failed.\n\n" + CBTestPage.errors);
        }
    }
};

document.addEventListener("DOMContentLoaded", CBTestPage.DOMContentDidLoad);

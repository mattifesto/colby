"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported Colby */


var Colby = {
    updateTimesTimeoutID: null,
    updateTimesCount: 0,
    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July',
                 'August', 'September', 'October', 'November', 'December'],

     /**
      * @param function callback
      *
      * @return undefined
      */
     afterDOMContentLoaded: function (callback) {
         if (!Colby.browserIsSupported) {
             return;
         }

         if (document.readyState === "loading") {
             document.addEventListener("DOMContentLoaded", callback);
         } else {
             callback();
         }
     },

    /**
     * @param string text
     *
     * @return undefined
     */
    alert: function (text) {
        Colby.setPanelText(text);
        Colby.showPanel();
    },
    /* alert() */


    /**
     * This function is often used with bind() to create a single callback from
     * multiple callbacks.
     *
     * @param [function] callbacks
     *
     * @return function
     */
    call: function (callbacks) {
        callbacks.forEach(function (callback) { callback.call(); });
    },

    /**
     * @param string className
     * @param string functionName
     * @param object? args
     * @param File? file
     *
     *      A File usually retrieved from an input element.
     *
     *      https://developer.mozilla.org/en-US/docs/Web/API/File
     *
     * @return Promise
     */
    callAjaxFunction: function (functionClassName, functionName, args, file) {
        var formData = new FormData();
        formData.append("ajax", JSON.stringify({
            functionClassName: functionClassName,
            functionName: functionName,
            args: args,
        }));

        if (file !== undefined) {
            formData.append("file", file);
        }

        return Colby.fetchAjaxResponse("/", formData)
            .then(onFulfilled);

        function onFulfilled(response) {
            return response.value;
        }
    },


    /**
     * @param hex160 ID
     * @param string basename
     * @param string flexdir
     *
     * @return string
     */
    dataStoreFlexpath : function (ID, basename, flexdir) {
        var flexpath = ID.replace(/^(..)(..)/, "data/$1/$2/");

        if (basename) {
            flexpath = flexpath + "/" + basename;
        }

        if (flexdir) {
            flexpath = flexdir + "/" + flexpath;
        }

        return flexpath;
    },

    /**
     * @return string
     */
    dateToLocaleString: function (date) {
        return Colby.dateToLocaleDateString(date) + " " + Colby.dateToLocaleTimeString(date);
    },

    /**
     * Use this function with promises to display error messages to the user and
     * report the error to the server.
     *
     *      callAjaxFunction().catch(Colby.displayAndReportError)
     *
     * @param Error error
     *
     * @return undefined
     */
    displayAndReportError: function (error) {
        Colby.displayError(error);
        Colby.reportError(error);
    },

    /**
     * Use this function with promises to display error messages to the user.
     *
     *      callAjaxFunction().catch(Colby.displayError)
     *
     * @param Error error
     *
     * @return undefined
     */
    displayError: function (error) {
        if (!Colby.browserIsSupported) {
            return;
        }

        if (error.ajaxResponse) {
            Colby.displayResponse(error.ajaxResponse);
        } else {
            Colby.alert(
                Colby.errorToMessage(error)
            );
        }
    },
    /* displayError() */


    /**
     * @param object ajaxResponse
     *
     * @return undefined
     */
    displayResponse: function (ajaxResponse) {
        var element, message, button;
        if ('stackTrace' in ajaxResponse) {
            element                     = document.createElement("div");
            message                     = document.createElement("p");
            message.style.textAlign     = "center";
            message.style.marginBottom  = "100px";
            message.textContent         = ajaxResponse.message;

            element.appendChild(message);

            if (ajaxResponse.classNameForException === "CBModelVersionMismatchException") {
                button                  = document.createElement("button");
                button.textContent      = "Reload";
                button.style.display    = "block";
                button.style.margin     = "20px auto";

                button.addEventListener("click", function() { location.reload(); });

                element.appendChild(button);
            } else {
                var stack               = document.createElement("pre");
                stack.style.fontSize    = "13px";
                stack.textContent       = ajaxResponse.stackTrace;

                element.appendChild(stack);
            }

            Colby.setPanelElement(element);
        } else if (ajaxResponse.userMustLogIn) {
            element                 = document.createElement("div");
            message                 = document.createElement("p");
            message.style.textAlign = "center";
            message.textContent     = ajaxResponse.message;
            button                  = document.createElement("button");
            button.textContent      = "Reload";
            button.style.display    = "block";
            button.style.margin     = "20px auto";

            button.addEventListener("click", function() { location.reload(); });

            element.appendChild(message);
            element.appendChild(button);

            Colby.setPanelElement(element);
        } else {
            Colby.setPanelText(ajaxResponse.message);
        }

        Colby.showPanel();
    },

    /**
     * This function can be used as a handler for XHR.onerror
     *
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    displayXHRError : function (args) {
        Colby.displayResponse(Colby.responseFromXMLHttpRequest(args.xhr));
    },

    /**
     * @param Element element
     *
     * @return int|null
     */
    elementToTimestamp : function (element) {
        var timestamp = parseInt(element.getAttribute("data-timestamp"), 10);

        return isNaN(timestamp) ? null : timestamp;
    },

    /**
     * Converts an error object to a CBJavaScriptError model.
     *
     * History:
     *
     *      An initial goal was to stringify and Error object and send it to an
     *      ajax function. But when an Error object is stringified it doesn't
     *      serialize all of its properties.
     *
     *      Additional information that is not contained in the Error object is
     *      added to the model returned by this function.
     *
     * @param Error error
     *
     * @return object (CBJavaScriptError)
     */
    errorToCBJavaScriptErrorModel: function (error) {
        return {
            className: 'CBJavaScriptError',
            column: error.column,
            line: error.line,
            message: error.message,
            pageURL: location.href,
            sourceURL: error.sourceURL,
            stack: error.stack,
        };
    },

    /**
     * @param Error error
     *
     * @return string
     */
    errorToMessage: function (error) {
        var message = error.message || "(no message)";
        var basename = error.sourceURL ? error.sourceURL.split(/[\\/]/).pop() : "(no sourceURL)";
        var line = error.line || "(no line)";

        return "\"" + message + "\" in " + basename + " line " + line;
    },

    /**
     * This function is the recommended way to make an Ajax request for Colby.
     * To alleviate error notifications in situations where customers have less
     * stable internet service, this function will attempt the Ajax request
     * up to three times if errors occur.
     *
     * @param string URL
     * @param any? data
     *
     *      The data can be of any form accepted by the XMLHttpRequest.send()
     *      function. Most commonly, if used, it will be a FormData instance.
     *
     * @return Promise
     *
     *      Returns a promise that passes an 'ajax response' object (created by
     *      this class) to resolve handlers. If an error occurs for any reason a
     *      JavaScript Error object is passed to reject handlers with an 'ajax
     *      response' object set to the Error's `ajaxResponse` propery.
     */
    fetchAjaxResponse: function (URL, data) {
        return new Promise(function (resolve, reject) {
            var fetchCount = 0;
            var xhr;

            fetch();

            function fetch() {
                xhr = new XMLHttpRequest();
                xhr.onloadend = handleLoadEnd;
                xhr.open("POST", URL);
                xhr.send(data);

                fetchCount += 1;
            }

            function handleLoadEnd() {
                if (xhr.status === 0 && fetchCount < 3) {
                    fetch();
                    return;
                }

                var ajaxResponse = Colby.responseFromXMLHttpRequest(xhr);

                if (ajaxResponse.wasSuccessful) {
                    resolve(ajaxResponse);
                } else {
                    var error = new Error(ajaxResponse.message);
                    error.ajaxResponse = ajaxResponse;

                    reject(error);
                }
            }
        });
    },


    /**
     * @return bool
     */
    localStorageIsSupported: function () {
        if (Colby.cachedLocalStorageIsSupported === undefined) {
            var value = "value";

            try {
                localStorage.setItem(value, value);
                localStorage.removeItem(value);
                Colby.cachedLocalStorageIsSupported = true;
            } catch(e) {
                Colby.cachedLocalStorageIsSupported = false;
            }
        }

        return Colby.cachedLocalStorageIsSupported;
    },

    /**
     * @return string
     */
    get nonBreakingSpace() {
        return "\u00A0";
    },

    /**
     * This method generates a random hex string representing a 160-bit number
     * which is the same length as a SHA-1 hash and can be used as a unique ID.
     *
     * @return hex160
     */
    random160: function () {
        var i;
        var randomNumbers;

        if (typeof Uint16Array !== undefined && window.crypto && window.crypto.getRandomValues) {
            randomNumbers = new Uint16Array(10);

            window.crypto.getRandomValues(randomNumbers);
        } else {
            randomNumbers = [];

            for (i = 0; i < 10; i++) {
                var uint16 = Math.floor(Math.random() * 0xffff);

                randomNumbers.push(uint16);
            }
        }

        var random160 = "";

        for (i = 0; i < 10; i++) {
            var hex = randomNumbers[i].toString(16);
            hex = "0000".substr(0, 4 - hex.length) + hex;
            random160 = random160 + hex;
        }

        return random160;
    },

    /**
     * @deprecated use Colby.reportError()
     */
    report: function (error) {
        Colby.reportError(error);
    },

    /**
     * Use this function to report an error to the server.
     *
     *      callAjaxFunction().catch(Colby.reportError)
     *
     * This function will filter out errors created in reponse to a failed Ajax
     * request because the server generated and previously logged those errors
     * during the request.
     *
     * @param Error error
     *
     * @return undefined
     *
     *      This function does not return the promise it creates because it is
     *      not meant to be inserted into promise chains.
     */
    reportError: function (error) {
        if (!Colby.browserIsSupported) {
            return;
        }

        if (error.ajaxResponse) { // Filter out Ajax errors
            return;
        }

        Colby.callAjaxFunction(
            "CBJavaScript",
            "reportError",
            {
                errorModel: Colby.errorToCBJavaScriptErrorModel(error),
            }
        );
    },

    /**
     * The requestTimeUpdate() function tells Colby that you have a relative
     * time value that you wish to update. Using the function is an alternative
     * to using the unixTimestampToElement() function.
     *
     * The use case for this function is when you need to update a time string
     * but cannot insert an element to do so.
     *
     * This function restarts time updates by calling Colby.updateTimes(true).
     * The comments for that function have a description of the frequency of
     * updates.
     *
     * @param function callback
     *
     *      The callback will be passed a single parameter of a JavaScript
     *      timestamp of the current time that is associated with the update.
     *
     * @return undefined
     */
    requestTimeUpdate: function (callback) {
        if (typeof callback === "function") {
            if (Colby.timeUpdateCallbacks === undefined) {
                Colby.timeUpdateCallbacks = [];
            }

            Colby.timeUpdateCallbacks.push(callback);
            Colby.updateTimes(true);
        }
    },

    /**
     * @return object
     *
     *      {
     *          message: string,
     *          wasSuccessful: bool,
     *          xhr: XMLHttpRequest,
     *      }
     */
    responseFromXMLHttpRequest: function (xhr) {
        var response;

        switch (xhr.status) {
            case 0:
                response = {
                    className : "CBAjaxResponse",
                    message : "An error occured when making an Ajax request " +
                              "to the server. This was most likely caused by " +
                              "a network issue or less likely caused by the " +
                              "server domain name in the request URL being " +
                              "incorrect.",
                    wasSuccessful : false,
                };
                break;
            case 200:
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (error) {
                    response = {
                        className: "CBAjaxResponse",
                        message: "An Ajax request to the server returned " +
                                 "without error but the xhr.responseText is " +
                                 "not valid JSON.",
                        wasSuccessful: false,
                    };
                }
                break;
            default:
                response = {
                    className : "CBAjaxResponse",
                    message : "An Ajax request to the server returned an " +
                              "unexpected response with the status code " +
                              xhr.status + " and the status text: \"" +
                              xhr.statusText + "\".",
                    wasSuccessful : false,
                };
                break;
        }

        response.xhr = xhr;

        return response;
    },

    /**
     * @return object
     *
     *      {
     *          init()-> undefined
     *
     *              Called after DOMContentLoaded. If another JavaScript file
     *              has called stop() before DOMContentLoaded, this function
     *              will not start processing tasks.
     *
     *          start() -> Promise -> undefined
     *
     *              If tasks aren't currently running, calling this function
     *              will start running tasks again until there aren't any. The
     *              promise returned will resolve when there are no more tasks
     *              to run.
     *
     *          stop() -> undefined
     *
     *              Will stop running tasks. This is useful when the session is
     *              running other operations on the server or is preparing to
     *              only run tasks for a specific process.
     *      }
     */
    get tasks() {
        let isStopped = false;
        let promise;

        if (Colby.CBTasks2_API) {
            return Colby.CBTasks2_API;
        }

        Colby.CBTasks2_API = {
            init: init,
            start: start,
            stop: stop,
        };

        return Colby.CBTasks2_API;

        /**
         * @return undefined
         */
        function init() {
            if (!isStopped) {
                start();
            }
        }

        /**
         * Under normal circumstances the promise returned by this function is
         * not used. But there are some situations where the caller of this
         * function wants all tasks to run until they are complete, potentially
         * for a specific process ID, and the to have the promise resolve.
         *
         * The model import process is one of these situations.
         *
         * @return Promise
         */
        function start() {
            isStopped = false;

            if (promise) {
                return promise;
            }

            promise = new Promise(function (resolve, reject) {
                return go();

                /**
                 * @return undefined
                 */
                function go() {

                    /**
                     * Errors occuring during this process are likely to be server
                     * side errors and will be reported on the server. If the
                     * promise is rejected further requests will be stopped. This
                     * process does not communicate with the end user.
                     */

                    let args = {
                        processID: Colby.CBTasks2_processID,
                    };

                    Colby.callAjaxFunction("CBTasks2", "runNextTask", args)
                        .then(goAgainOrResolve)
                        .catch(report);

                    Colby.CBTasks2_countOfTasksRequested += 1;
                }

                /**
                 * This function will determine if there is any reason to continue
                 * to attempt to run tasks. If there is, it will go again; if not,
                 * it will resolve the promise.
                 *
                 * @return undefined
                 */
                function goAgainOrResolve(value) {
                    Colby.CBTasks2_countOfTasksRun += value.tasksRunCount;

                    if (isStopped || value.tasksRunCount === 0) {
                        promise = undefined;
                        resolve();
                    } else {
                        setTimeout(go, Colby.CBTasks2_delay);
                    }
                }

                /**
                 * @return undefined
                 */
                function report(error) {
                    promise = undefined;

                    Colby.report(error);

                    reject(error);
                }
            });

            return promise;
        }

        /**
         * @return undefined
         */
        function stop() {
            isStopped = true;
        }
    },

    /**
     * @param string text
     *
     * @return string
     */
    textToHTML: function (text) {
        var map = {
           '&': '&amp;',
           '<': '&lt;',
           '>': '&gt;',
           '"': '&quot;',
           "'": '&#039;'
         };

         return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    },

    /**
     * If you want to receive the time update callback directly instead of using
     * the element returned by this function use Colby.requestTimeUpdate().
     *
     * @param int unixTimestamp
     * @param string defaultTextContent
     *
     *      This text will be displayed if there is no unix timestamp.
     *
     * @return Element
     */
    unixTimestampToElement: function (unixTimestamp, defaultTextContent) {
        var element = document.createElement("time");
        element.className = "time";
        element.textContent = "---";

        if (unixTimestamp) {
            element.dataset.timestamp = unixTimestamp * 1000;
        } else {
            element.dataset.nulltextcontent = defaultTextContent || "";
        }

        return element;
    },

    /**
     * This function converts a unix timestamp to a string that can be parsed
     * properly by Date.parse().
     *
     * @param int unixTimestamp
     *
     * @return string
     */
    unixTimestampToParseableDateString : function (unixTimestamp) {
        var date = new Date(unixTimestamp * 1000);

        return (date.getMonth() + 1) + "/" + date.getDate() + "/" + date.getFullYear();
    },

    /**
     * @return string
     */
    unixTimestampToParseableString : function (unixTimestamp) {
        return Colby.unixTimestampToParseableDateString(unixTimestamp) +
               " " +
               Colby.unixTimestampToParseableTimeString(unixTimestamp);
    },

    /**
     * @return string
     */
    unixTimestampToParseableTimeString : function (unixTimestamp) {
        var date = new Date(unixTimestamp * 1000);
        var hour = date.getHours() % 12;
        hour = hour ? hour : 12;
        var minutes = date.getMinutes().toString();

        if (minutes.length < 2) {
            minutes = '0'.concat(minutes);
        }

        var AMPM = (date.getHours() > 11) ? 'pm' : 'am';

        return hour + ':' + minutes + ' ' + AMPM;
    },

    /**
     * @param Element element
     * @param Date now?
     *
     * @return undefined
     */
    updateCBTimeElementTextContent: function (element, now) {
        if (now === undefined) {
            now = new Date();
        }

        let timestamp = Colby.elementToTimestamp(element);

        if (timestamp === null) {
            if (element.hasAttribute("data-nulltextcontent")) {
                element.textContent =
                    element.getAttribute("data-nulltextcontent");
            }

            return;
        }

        let date = new Date(timestamp);
        let args;

        if (element.classList.contains("compact")) {
            args = { "compact" : true };
        }

        element.textContent = Colby.dateToRelativeLocaleString(date, now, args);
    },

    /**
     * @param bool restart
     *
     *      Specify true to restart updates every second after adding new time
     *      elements the page.
     *
     *      After a restart, times will update once per second for 90 seconds
     *      and then once every 15 secones after that.
     *
     * @return undefined
     */
    updateTimes: function (restart) {
        if (Colby.updateTimesTimeoutID) {
            window.clearTimeout(Colby.updateTimesTimeoutID);
        }

        if (restart) {
            Colby.updateTimesCount = 0;
        } else {
            Colby.updateTimesCount += 1;
        }

        var elements = document.getElementsByClassName('time');
        var countOfElements = elements.length;
        let now = new Date();

        for (var i = 0; i < countOfElements; i++) {
            Colby.updateCBTimeElementTextContent(elements.item(i), now);
        }

        if (Array.isArray(Colby.timeUpdateCallbacks)) {
            let javascriptTimestamp = now.getTime();

            Colby.timeUpdateCallbacks.forEach(function (callback) {
                callback(javascriptTimestamp);
            });
        }

        /**
         * Do updates every second for the first 90 seconds. It's 90 instead of
         * 60 in for cases where the server clock and the client clock are
         * different.
         */

        if (Colby.updateTimesCount < 90) {
            Colby.updateTimesTimeoutID = window.setTimeout(Colby.updateTimes, 1000);
        } else {
            Colby.updateTimesTimeoutID = window.setTimeout(Colby.updateTimes, 15000);
        }
    },

    /**
     * This code is dangerous because it can provide an image structure for
     * a URI that doesn't represent an image.
     *
     * @param string URI
     *
     * @return object|undefined
     */
    URIToImage: function (URI) {
        var regex = /\/data\/([0-9a-f]{2})\/([0-9a-f]{2})\/([0-9a-f]{36})\/([^\/\.]+)\.([^\/\.]+)$/;
        var matches = URI.match(regex);

        if (matches) {
            return {
                className: "CBImage",
                extension: matches[5],
                filename: matches[4],
                ID: matches[1] + matches[2] + matches[3],
            };
        }
    },
    /* URIToImage() */
};
/* Colby */


/**
 * This function is used extend one object by appending the properites of
 * another object. It allows for more clear and safer code when adding multiple
 * properties and functions to an object by not having to specify the object
 * name for each property or function.
 *
 * @return Object
 */
Colby.extend = function(objectToExtend, objectWithProperties) {

    for (var property in objectWithProperties) {

        if (objectWithProperties.hasOwnProperty(property)) {

            objectToExtend[property] = objectWithProperties[property];
        }
    }
};


/**
 * This function is out of order to get the error handler set as soon as
 * possible.
 *
 * @NOTE unknown original date
 *
 *      Some errors will somehow disable this error handling. It's very odd.
 *      When this happens use debugging to find the error and once you fix it
 *      the error handling will start working again. I'm not sure exactly which
 *      errors cause this strange behavior.
 *
 *      @NOTE 2019.06.14 This hasn't been seen in a while.
 *
 * @NOTE 2016.12.28
 *
 *      Because this makes an asynchronous request it will not work if there is
 *      navigation immediately after which cancels the request.
 *
 *
 * @NOTE 2019.06.14
 *
 *      The properties of an error object such as column and line are currently
 *      not well documented. The code to add these properties has been left in
 *      despite discontinuation of IE 11 support because I don't have the time
 *      right now to do a full investigation.
 *
 * @return false
 *
 *      Returning false allows the firing of the default event handler. I don't
 *      remember right now why this is important and it was not originally
 *      documented.
 */
Colby.handleError = function(message, sourceURL, line, column, error) {
    if (typeof error !== "object" || error === null) { /* IE11 */
        error = {};
    }

    if (error.column === undefined) { /* IE11 */
        error.column = column;
    }

    if (error.line === undefined) { /* IE11 */
        error.line = line;
    }

    if (error.message === undefined) { /* IE11 */
        error.message = message;
    }

    if (error.sourceURL === undefined) { /* IE11 */
        error.sourceURL = sourceURL;
    }

    Colby.reportError(error);

    return false;
};
/* handleError() */


/**
 * Set the error handler as soon as possible
 * to catch errors even if they occur later in this file.
 */
window.onerror = Colby.handleError;

/**
 * @return null
 */
Colby.createPanel = function() {
    var panel = document.createElement("div");
    panel.className = "CBPanelView";
    var container = document.createElement("div");
    container.className = "CBPanelContainer CBLightTheme";
    var content = document.createElement("div");
    content.className = "CBPanelContent";
    var buttonView = document.createElement("div");
    buttonView.className = "CBPanelButtonView";
    var button = document.createElement("button");
    button.textContent = "Dismiss";

    buttonView.appendChild(button);
    container.appendChild(content);
    container.appendChild(buttonView);
    panel.appendChild(container);

    button.addEventListener("click", Colby.hidePanel);

    Colby.panel = panel;
    Colby.panelContent = content;
};

/**
 * @return string
 */
Colby.dataStoreIDToURI = function(dataStoreID)
{
    var regex = /(..)(..)(.*)/;
    var matches = regex.exec(dataStoreID);

    return "/data/" + matches[1] + "/" + matches[2] + "/" + matches[3];
};

/**
 * NOTE: 2015.03.24
 * The `toLocaleDateString` function can be used in the future when the
 * `locales` and `options` arguments are supported in all browsers. This can
 * be tracked at Mozilla's documentation for the function.
 *
 * @param Date date
 * @param object? args
 * @param bool args.compact
 *
 * @return string
 *  This method returns a date in the following format: "February 14, 2010".
 */
Colby.dateToLocaleDateString = function(date, args) {
    if (args === undefined) args = {};

    if (args.compact === true) {
        return date.getFullYear() + "/" +
            ("00" + (date.getMonth() + 1)).slice(-2) + "/" +
            ("00" + date.getDate()).slice(-2);
    } else {
        return Colby.monthNames[date.getMonth()] + " " +
            date.getDate() + ", " +
            date.getFullYear();
    }
};

/**
 * @return string
 */
Colby.dateToLocaleTimeString = function(date, args) {
    if (args === undefined) args = {};

    var formattedAMPM, formattedHour, formattedMinutes;
    formattedHour = date.getHours() % 12;
    formattedHour = formattedHour ? formattedHour : 12;
    formattedMinutes = ("00" + date.getMinutes()).slice(-2);

    if (args.compact === true) {
        formattedAMPM = (date.getHours() > 11) ? 'pm' : 'am';
        formattedHour = ("00" + formattedHour).slice(-2);
    } else {
        formattedAMPM = (date.getHours() > 11) ? ' p.m.' : ' a.m.';
    }

    return formattedHour + ':' + formattedMinutes + formattedAMPM;
};
/* dateToLocaleTimeString() */


/**
 * @return string
 */
Colby.dateToRelativeLocaleString = function (date, now, args) {
    if (args === undefined) args = {};
    var timespan = now.getTime() - date.getTime();
    var string;

    // date is in the future by more than 60 seconds
    if (timespan < (1000 * -60)) {
        string =
        Colby.dateToLocaleDateString(date) +
        ' ' +
        Colby.dateToLocaleTimeString(date);
    }

    // less than 60 seconds
    else if (timespan < (1000 * 60))
    {
        string = Math.floor(timespan / 1000) + ' seconds ago';
    }

    // less than 2 minutes
    else if (timespan < (1000 * 60 * 2))
    {
        string = '1 minute ago';
    }

    // less than 60 minutes
    else if (timespan < (1000 * 60 * 60))
    {
        string = Math.floor(timespan / (1000 * 60)) + ' minutes ago';
    }

    // less that 24 hours and today
    else if (
        timespan < (1000 * 60 * 60 * 24) &&
        date.getDate() == now.getDate()
    ) {
        string = 'Today at ' + Colby.dateToLocaleTimeString(date, args);
    }

    // less than 48 house and yesterday
    else if (
        timespan < (1000 * 60 * 60 * 24 * 2) &&
        ((date.getDay() + 1) % 7) == now.getDay()
    ) {
        string = 'Yesterday at ' + Colby.dateToLocaleTimeString(date, args);
    }

    // just return date and time
    else
    {
        string =
        Colby.dateToLocaleDateString(date, args) +
        ' ' +
        Colby.dateToLocaleTimeString(date, args);
    }

    return string;
};
/* dateToRelativeLocaleString() */


/**
 * @return undefined
 */
Colby.hidePanel = function () {
    if (
        Colby.panel &&
        Colby.panel.parentNode
    ) {
        Colby.panel.parentNode.removeChild(
            Colby.panel
        );
    }
};
/* hidePanel() */


/**
 * @return undefined
 */
Colby.setPanelElement = function(element) {
    if (Colby.panel === undefined) {
        Colby.createPanel();
    }

    Colby.panelContent.textContent = null;
    Colby.panelContent.appendChild(element);
};

/**
 * @return undefined
 */
Colby.setPanelText = function(text) {
    Colby.setPanelElement(document.createTextNode(text));
};

/**
 * @return undefined
 */
Colby.showPanel = function() {
    if (Colby.panel === undefined) {
        Colby.createPanel();
    }

    if (!Colby.panel.parentNode) {
        document.body.appendChild(Colby.panel);
    }
};

/**
 * TODO: Reconcile with CBConvert::stringToURI()
 *
 * @param string? text
 *
 * @return string
 */
Colby.textToURI = function (text) {
    text = (typeof text === "string") ? text.trim() : "";

    /**
     * Colby limits URIs to 100 characters, we'll reduce the text to 80
     * characters to leave room for multi-byte characters.
     */

    text = text.substr(0, 80);

    /**
     * Convert all characters to lowercase to start the URI string.
     */

    var uri = text.toLowerCase();

    /**
     * Replace ampersands surrounded by white space with the word "and"
     */

    uri = uri.replace(/\s&\s/g, ' and ');

    /**
     * Remove all characters from the URI string except lowercase letters,
     * numbers, forward slashes, hyphens, and spaces.
     */

    uri = uri.replace(/[^a-z0-9\/\-\ ]/g, '');

    /**
     * Remove all of the adjacent forward slashes, hyphens, and spaces from
     * the beginning and the end of the URI.
     *
     * Example:
     *  '//--blog/my-day/ ' --> 'blog/my-day'
     */

    uri = uri.replace(/^[\/\-\ ]+|[\/\-\ ]+$/g, '');

    /**
     * Replace all adjacent hyphens, spaces, and forward slashes containing
     * at least one forward slash with a single forward slash.
     *
     * Example:
     *  'blog--/---- / - /  -my-day' --> 'blog/my-day'
     */

    uri = uri.replace(/[\-\ ]*\/[\/\-\ ]+/g, '/');

    /**
     * Replace all adjacent hyphens and spaces with a single hypen.
     *
     * Example:
     *  'blog/my- - - - ----- -day' --> 'blog/my-day'
     */

    uri = uri.replace(/[\-\ ]+/g, '-');

    return uri;
};
/* Colby */


/**
 * General page loaded tasks
 */

Colby.afterDOMContentLoaded(function () {
    Colby.updateTimes(true);
});

/**
 * CBTasks2 run tasks
 */

Colby.CBTasks2_countOfTasksRequested = 0;
Colby.CBTasks2_countOfTasksRun = 0;
Colby.CBTasks2_delay = 5000;
Colby.CBTasks2_processID = undefined;

Colby.afterDOMContentLoaded(function () { Colby.tasks.init(); });


/* initialize */
(function init() {

    /**
     * @NOTE 2019.06.14
     *
     *      Browsers must natively support Promises. This requirement makes
     *      Internet Explorer 11 an unsupported browser.
     */

    let browserIsSupported = false;

    if (
        typeof Promise !== "undefined" &&
        Promise.toString().indexOf("[native code]") !== -1
    ) {
        browserIsSupported = true;
    } else {
        Colby.alert(
            "The web browser you are using is no longer supported by this" +
            " website. Upgrade to a recent version of Chrome, Edge, Firefox," +
            " or Safari."
        );
    }

    Object.defineProperty(
        Colby,
        "browserIsSupported",
        {
            configurable: true,
            enumerable: false,
            get: function () { return browserIsSupported; },
        }
    );

})();
/* initialize */

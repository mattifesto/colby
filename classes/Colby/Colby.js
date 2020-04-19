"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported Colby */
/* global
    CBAjax,
    console,
*/



(function () {

    /**
     * Set the error handler as soon as possible to catch errors even if they
     * occur later in this file. If CBErrorHandler is loaded it will gracefully
     * replace this handler.
     */
    window.addEventListener(
        "error",
        handleError
    );

    /**
     * The reportError() makes an Ajax request which will not complete if there
     * is navigation immediately after because navigation cancels active
     * requests.
     *
     * @param ErrorEvent errorEvent
     *
     * @return undefined
     *
     *      @NOTE 2020_02_26
     *
     *      It was recently very roughly documented here that the return value
     *      of this function was boolean and had some effect, but according to
     *      the linked documentation there is not supposed to be a return value
     *      for this function. It makes sense because there could be potentially
     *      many listeners added to the error event that could have different
     *      return values.
     *
     *      https://mzl.la/2VosKqz
     */
    function handleError(
        errorEvent
    ) {
        Colby.reportError(errorEvent.error);
    }
    /* handleError() */

})();



var Colby = {
    updateTimesTimeoutID: null,
    updateTimesCount: 0,

    monthNames: [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ],

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
            document.addEventListener(
                "DOMContentLoaded",
                callback
            );
        } else {
            callback();
        }
    },
    /* afterDOMContentLoaded() */



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
     * @deprecated 2020_04_18
     *
     *      Use CBAjax.call().
     */
    callAjaxFunction: function (
        functionClassName,
        functionName,
        functionArguments,
        file
    ) {
        return CBAjax.call(
            functionClassName,
            functionName,
            functionArguments,
            file
        );
    },
    /* callAjaxFunction() */



    /**
     * @NOTE 2015_03_24
     *
     *      The toLocaleDateString() function can be used in the future when the
     *      "locales" and "options" arguments are supported in all browsers.
     *      This can be tracked at Mozilla's documentation for the function.
     *
     * @param Date date
     * @param object args
     *
     *      {
     *          compact: bool
     *      }
     *
     * @return string
     *
     *      example: "February 14, 2010".
     */
    dateToLocaleDateString: function (date, args) {
        if (args && args.compact === true) {
            return date.getFullYear() +
                "/" +
                ("00" + (date.getMonth() + 1)).slice(-2) +
                "/" +
                ("00" + date.getDate()).slice(-2);
        } else {
            return Colby.monthNames[date.getMonth()] +
                " " +
                date.getDate() +
                ", " +
                date.getFullYear();
        }
    },
    /* dateToLocaleDateString() */


    /**
     * @return string
     */
    dateToLocaleString: function (date) {
        let localeString =
        Colby.dateToLocaleDateString(date) +
        " " +
        Colby.dateToLocaleTimeString(date);

        return localeString;
    },


    /**
     * @param Date date
     * @param object args
     *
     *      {
     *          compact: bool
     *      }
     *
     * @return string
     */
    dateToLocaleTimeString: function (date, args) {
        let formattedAMPM;

        let formattedHour = date.getHours() % 12;
        formattedHour = formattedHour ? formattedHour : 12;

        let formattedMinutes = ("00" + date.getMinutes()).slice(-2);

        if (args && args.compact === true) {
            formattedAMPM = (date.getHours() > 11) ? 'pm' : 'am';
            formattedHour = ("00" + formattedHour).slice(-2);
        } else {
            formattedAMPM = (date.getHours() > 11) ? ' p.m.' : ' a.m.';
        }

        return formattedHour + ':' + formattedMinutes + formattedAMPM;
    },
    /* dateToLocaleTimeString() */


    /**
     * @param Date date
     * @param Date now
     * @param object args
     *
     *      {
     *          compact: bool
     *      }
     *
     * @return string
     */
    dateToRelativeLocaleString: function (date, now, args) {
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
    },
    /* dateToRelativeLocaleString() */



    /**
     * @param Element element
     *
     * @return int|null
     */
    elementToTimestamp: function (element) {
        var timestamp = parseInt(element.getAttribute("data-timestamp"), 10);

        return isNaN(timestamp) ? null : timestamp;
    },



    /**
     * Converts an error object to a CBJavaScriptError model.
     *
     * Properties:
     *
     *      Safari          Firefox         Chrome
     *      ------          -------         ------
     *      column          columnNumber    no
     *      line            lineNumber      no
     *      sourceURL       filename        no
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
     *      The ErrorEvent object passed to the listener of the "error" event
     *      has some standardized properties that are similar, but not all
     *      errors are handled by an error event listener. The "stack" property
     *      actually contains all the data but has a different format on Chrome
     *      browsers.
     *
     * @param Error error
     *
     * @return object (CBJavaScriptError)
     */
    errorToCBJavaScriptErrorModel: function (error) {
        let lineNumber = (
            error.line === undefined ?
            error.lineNumber :
            error.line
        );

        let columnNumber = (
            error.column === undefined ?
            error.columnNumber :
            error.column
        );

        let sourceURL = (
            error.sourceURL === undefined ?
            error.fileName :
            error.sourceURL
        );

        return {
            className: 'CBJavaScriptError',
            column: columnNumber,
            line: lineNumber,
            message: error.message,
            pageURL: location.href,
            sourceURL: sourceURL,
            stack: error.stack,
        };
    },
    /* errorToCBJavaScriptErrorModel() */



    /**
     * @param Error error
     *
     * @return string (text)
     */
    errorToMessage: function (error) {
        var message = error.message || "(no message)";

        var basename =
        error.sourceURL ?
        error.sourceURL.split(/[\\/]/).pop() :
        "(no sourceURL)";

        var line = error.line || "(no line)";

        return "\"" + message + "\" in " + basename + " line " + line;
    },
    /* errorToMessage() */



    /**
     * @deprecated 2020_04_18
     *
     *      Use CBAjax.call() or CBAjax.fetchResponse() for transitional
     *      purposes.
     */
    fetchAjaxResponse(
        URL,
        data
    ) {
        return CBAjax.fetchResponse(
            URL,
            data
        );
    },
    /* fetchAjaxResponse() */



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
    /* localStorageIsSupported() */



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

        if (
            typeof Uint16Array !== undefined &&
            window.crypto &&
            window.crypto.getRandomValues
        ) {
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
     * @deprecated
     *
     *      Use CBErrorHandler.report()
     */
    report: function (error) {
        Colby.reportError(error);
    },



    /**
     * @deprecated 2020_02_28
     *
     *      Use CBErrorHandler.report(). This function must contain the
     *      reporting code until all uses of it and Colby.report() are replaced
     *      with calls to CBErrorHandler.report().
     *
     * Use this function to report an error to the server.
     *
     *      callAjaxFunction(
     *          ...
     *      ).catch(
     *          function (error) {
     *              Colby.reportError(error);
     *          }
     *      );
     *
     * This function will filter out errors created in response to a failed Ajax
     * request because the server generated and previously logged those errors
     * during the request.
     *
     * @param Error error
     *
     * @return Promise -> undefined
     *
     *      This function returns a promise that will resolve when the request
     *      to the server to report the error has completed. This is generally
     *      not an important promise but may be important in cases where you
     *      want to report the error and wait to navigate to another page so you
     *      don't cancal the report request to the server.
     */
    reportError: function (error) {
        if (!Colby.browserIsSupported) {
            return;
        }

        if (error.ajaxResponse) { // Filter out Ajax errors
            return;
        }

        let promise = Colby.callAjaxFunction(
            "CBJavaScript",
            "reportError",
            {
                errorModel: Colby.errorToCBJavaScriptErrorModel(error),
            }
        ).catch(
            function (error) {
                console.log("Colby.reportError() Ajax request failed.");
                console.log(error.message);
            }
        );

        return promise;
    },
    /* reportError() */



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
    /* requestTimeUpdate() */



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

            promise = new Promise(
                function (resolve) {
                    return go();


                    /* -- closures -- -- -- -- -- */



                    /**
                     * @return undefined
                     */
                    function go() {

                        /**
                         * Errors occuring during this process are likely to be
                         * server side errors and will be reported on the
                         * server. If the promise is rejected further requests
                         * will be stopped. This process does not communicate
                         * with the end user.
                         */

                        let args = {
                            processID: Colby.CBTasks2_processID,
                        };

                        Colby.callAjaxFunction(
                            "CBTasks2",
                            "runNextTask",
                            args
                        ).then(
                            goAgainOrResolve
                        ).catch(
                            function (error) {
                                promise = undefined;

                                Colby.reportError(error);
                            }
                        );

                        Colby.CBTasks2_countOfTasksRequested += 1;
                    }
                    /* go() */



                    /**
                     * This function will determine if there is any reason to
                     * continue to attempt to run tasks. If there is, it will go
                     * again; if not, it will resolve the promise.
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
                    /* goAgainOrResolve() */
                }
            );

            return promise;
        }

        /**
         * @return undefined
         */
        function stop() {
            isStopped = true;
        }
    },
    /* get tasks */


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
     * @TODO Reconcile with CBConvert::stringToURI()
     *
     * @param string text
     *
     * @return string
     */
    textToURI: function (text) {
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
    },
    /* textToURI() */


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
    unixTimestampToElement: function (
        unixTimestamp,
        defaultTextContent,
        className
    ) {
        let element = document.createElement("time");

        if (typeof className === "string") {
            element.className = "time " + className;
        } else {
            element.className = "time";
        }

        unixTimestamp = Number(unixTimestamp);

        if (Number.isInteger(unixTimestamp)) {
            element.dataset.timestamp = unixTimestamp * 1000;
            Colby.updateTimes(/* restart: */ true);
        } else {
            element.dataset.nulltextcontent = defaultTextContent || "";
        }

        Colby.updateCBTimeElementTextContent(element);

        return element;
    },
    /* unixTimestampToElement() */


    /**
     * This function converts a unix timestamp to a string that can be parsed
     * properly by Date.parse().
     *
     * @param int unixTimestamp
     *
     * @return string
     */
    unixTimestampToParseableDateString: function (unixTimestamp) {
        var date = new Date(unixTimestamp * 1000);

        let parseableDateString =
        (date.getMonth() + 1) +
        "/" +
        date.getDate() +
        "/" +
        date.getFullYear();

        return parseableDateString;
    },


    /**
     * @return string
     */
    unixTimestampToParseableString: function (unixTimestamp) {
        let parseableString =
        Colby.unixTimestampToParseableDateString(unixTimestamp) +
        " " +
        Colby.unixTimestampToParseableTimeString(unixTimestamp);

        return parseableString;
    },


    /**
     * @return string
     */
    unixTimestampToParseableTimeString: function (unixTimestamp) {
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
            args = { "compact": true };
        }

        element.textContent =
        Colby.dateToRelativeLocaleString(date, now, args);
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
            Colby.updateTimesTimeoutID = window.setTimeout(
                Colby.updateTimes,
                1000
            );
        } else {
            Colby.updateTimesTimeoutID = window.setTimeout(
                Colby.updateTimes,
                15000
            );
        }
    },
    /* updateTimes() */



    /**
     * This code is dangerous because it can provide an image structure for
     * a URI that doesn't represent an image.
     *
     * @param string URI
     *
     * @return object|undefined
     */
    URIToImage(
        URI
    ) {
        let regex = new RegExp(
            "/" +
            "data" +
            "/" +
            "([0-9a-f]{2})" +
            "/" +
            "([0-9a-f]{2})" +
            "/" +
            "([0-9a-f]{36})" +
            "/" +
            "([^/\\.]+)" +
            "\\." +
            "([^/\\.]+)" +
            "$"
        );

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



/* initialize */
(function init() {

    /**
     * @NOTE 2019_06_14
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
        window.alert(
            "The web browser you are using is no longer supported by this" +
            " website. Use a recent version a regularly maintained browser" +
            " such as Chrome, Edge, Firefox, or Safari."
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

    Colby.CBTasks2_countOfTasksRequested = 0;
    Colby.CBTasks2_countOfTasksRun = 0;
    Colby.CBTasks2_delay = 5000;
    Colby.CBTasks2_processID = undefined;

    Colby.afterDOMContentLoaded(
        function () {
            Colby.updateTimes(true);
            Colby.tasks.init();
        }
    );

})();
/* initialize */

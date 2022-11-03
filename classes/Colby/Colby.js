/* global
    CBAjax,
    CBErrorHandler,
    CBException,
    CBID,
    CBJavaScript,
*/



(function ()
{
    "use strict";



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
        CBErrorHandler.report(
            errorEvent.error
        );
    }
    /* handleError() */




    let Colby =
    {
        tasks:
        Colby_createTasksController(),

        updateTimesTimeoutID:
        null,

        updateTimesCount:
        0,

        monthNames:
        [
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
         * @deprecated 2022_01_29
         *
         *      Use CBJavaScript.afterDOMContentLoaded()
         */
        afterDOMContentLoaded(
            callback
        ) {
            CBJavaScript.afterDOMContentLoaded(
                callback
            );
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
        call(
            callbacks
        ) {
            callbacks.forEach(
                function (
                    callback
                ) {
                    callback.call();
                }
            );
        },
        /* call() */



        /**
         * @deprecated 2020_04_18
         *
         *      Use CBAjax.call().
         */
        callAjaxFunction(
            functionClassName,
            functionName,
            executorArguments,
            file
        ) {
            return CBAjax.call(
                functionClassName,
                functionName,
                executorArguments,
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
         *          Colby_time_element_style: string
         *      }
         *
         * @return string
         *
         *      example: "February 14, 2010".
         */
        dateToLocaleDateString(
            date,
            args
        ) {
            let timeElementStyle = "Colby_time_element_style_default";

            if (args !== undefined) {
                timeElementStyle = args.Colby_time_element_style;
            }

            if (
                timeElementStyle === "Colby_time_element_style_compact"
            ) {
                return (
                    date.getFullYear() +
                    "/" +
                    ("00" + (date.getMonth() + 1)).slice(-2) +
                    "/" +
                    ("00" + date.getDate()).slice(-2)
                );
            } else if (
                timeElementStyle === "Colby_time_element_style_moment"
            ) {
                return new Intl.DateTimeFormat(
                    [],
                    {
                        month: "short",
                        day: "numeric",
                        year: "numeric",
                    }
                ).format(
                    date
                );
            } else {
                return (
                    Colby.monthNames[date.getMonth()] +
                    " " +
                    date.getDate() +
                    ", " +
                    date.getFullYear()
                );
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
         *          Colby_time_element_style: string
         *      }
         *
         * @return string
         */
        dateToLocaleTimeString(
            date,
            args
        ) {
            let formattedAMPM;

            let formattedHour = date.getHours() % 12;
            formattedHour = formattedHour ? formattedHour : 12;

            let formattedMinutes = ("00" + date.getMinutes()).slice(-2);

            if (
                args !== undefined &&

                args.Colby_time_element_style === (
                    "Colby_time_element_style_compact"
                )
            ) {
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
         *          Colby_time_element_style: string
         *      }
         *
         * @return string
         */
        dateToRelativeLocaleString(
            date,
            now,
            args
        ) {
            let timeElementStyle = "Colby_time_element_style_default";

            if (
                args !== undefined
            ) {
                timeElementStyle = args.Colby_time_element_style;
            }

            var timespan = now.getTime() - date.getTime();
            var string;

            if (
                timeElementStyle === "Colby_time_element_style_moment"
            ) {
                let millisecondsSinceDate = timespan;

                if (
                    millisecondsSinceDate < 0 /* in the future */
                ) {
                    return "0s";
                }

                else if (
                    millisecondsSinceDate < (1000 * 60) /* 60 seconds */
                ) {
                    let seconds = Math.floor(
                        millisecondsSinceDate / 1000
                    );

                    return `${seconds}s`;
                }

                else if (
                    millisecondsSinceDate < (1000 * 60 * 60) /* 60 minutes */
                ) {
                    let minutes = Math.floor(
                        millisecondsSinceDate / (1000 * 60)
                    );

                    return `${minutes}m`;
                }

                else if (
                    millisecondsSinceDate < (1000 * 60 * 60 * 24) /* 24 */
                ) {
                    let hours = Math.floor(
                        millisecondsSinceDate / (1000 * 60 * 60)
                    );

                    return `${hours}h`;
                }

                return new Intl.DateTimeFormat(
                    [],
                    {
                        month: "short",
                        day: "numeric",
                        year: "numeric",
                    }
                ).format(
                    date
                );
            }

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
         * @deprecated 2020_11_26
         *
         *      Use CBErrorHandler.errorToCBJavaScriptErrorModel()
         */
        errorToCBJavaScriptErrorModel(
            error
        ) {
            return CBErrorHandler.errorToCBJavaScriptErrorModel(
                error
            );
        },
        /* errorToCBJavaScriptErrorModel() */



        /**
         * @deprecated 2022_01_29
         *
         *      Use CBException.errorToOneLineErrorReport()
         *
         * @param Error error
         *
         * @return string (text)
         */
        errorToMessage(
            error
        ) {
            return CBException.errorToOneLineErrorReport(
                error
            );
        },
        /* errorToMessage() */



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
         * @deprecated 2020_12_21
         *
         *      Use CBID.generateRandomCBID()
         *
         * @return hex160
         */
        random160: function () {
            return CBID.generateRandomCBID();
        },



        /**
         * @deprecated
         *
         *      Use CBErrorHandler.report()
         */
        report: function (error) {
            CBErrorHandler.report(
                error
            );
        },



        /**
         * @deprecated 2020_04_25
         *
         *      Use CBErrorHandler.report().
         *
         *      This function will be moved to CBErrorHandler.report(). See
         *      CBErrorHandler.js for the steps required for the transition.
         *
         * Use this function to report an error to the server.
         *
         *      callAjaxFunction(
         *          ...
         *      ).catch(
         *          function (error) {
         *              CBErrorHandler.report(
         *                  error
         *              );
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
        reportError: function (
            error
        ) {
            return CBErrorHandler.report(
                error
            );
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
         * @deprecated 2022_02_24
         *
         *      Use CBConvert.stringToHTML()
         *
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
        /* textToHTML() */



        /**
         * @deprecated 2021_08_08
         *
         *      Use CBConvert.stringToURI()
         *
         * @param string text
         *
         * @return string
         */
        textToURI(
            text
        ) {
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
         * This function parallels ColbyConvert::timestampToHTML()
         *
         * If you want to receive the time update callback directly instead of using
         * the element returned by this function use Colby.requestTimeUpdate().
         *
         * @param int unixTimestamp
         * @param string defaultTextContent
         *
         *      This text will be displayed if there is no unix timestamp.
         *
         * @param string className
         *
         *      Colby_time_element_style_default
         *      Colby_time_element_style_compact
         *      Colby_time_element_style_moment
         *
         * @return Element
         */
        unixTimestampToElement(
            unixTimestamp,
            defaultTextContent,
            className
        ) // -> Element
        {
            let element =
            document.createElement(
                "time"
            );

            if (
                typeof className === "string"
            )
            {
                element.className =
                "time " +
                className;
            }

            else
            {
                element.className =
                "time";
            }

            unixTimestamp =
            Number(
                unixTimestamp
            );

            if (
                Number.isInteger(
                    unixTimestamp
                )
            )
            {
                element.dataset.timestamp =
                unixTimestamp *
                1000;

                Colby.updateTimes(
                    /* restart: */ true
                );
            } else {
                element.dataset.nulltextcontent =
                defaultTextContent ||
                "";
            }

            Colby.updateCBTimeElementTextContent(
                element
            );

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
        unixTimestampToParseableDateString(
            unixTimestamp
        ) {
            var date = new Date(
                unixTimestamp * 1000
            );

            let parseableDateString = (
                (date.getMonth() + 1) +
                "/" +
                date.getDate() +
                "/" +
                date.getFullYear()
            );

            return parseableDateString;
        },
        /* unixTimestampToParseableDateString() */



        /**
         * @return string
         */
        unixTimestampToParseableString(
            unixTimestamp
        ) {
            let parseableString = (
                Colby.unixTimestampToParseableDateString(
                    unixTimestamp
                ) +
                " " +
                Colby.unixTimestampToParseableTimeString(
                    unixTimestamp
                )
            );

            return parseableString;
        },
        /* unixTimestampToParseableString() */



        /**
         * @return string
         */
        unixTimestampToParseableTimeString(
            unixTimestamp
        ) {
            var date = new Date(
                unixTimestamp * 1000
            );

            var hour = date.getHours() % 12;

            hour = hour ? hour : 12;

            var minutes = date.getMinutes().toString();

            if (
                minutes.length < 2
            ) {
                minutes = '0'.concat(minutes);
            }

            var AMPM = (date.getHours() > 11) ? 'pm' : 'am';

            return hour + ':' + minutes + ' ' + AMPM;
        },
        /* unixTimestampToParseableTimeString() */



        /**
         * @param Element element
         * @param Date now?
         *
         * @return undefined
         */
        updateCBTimeElementTextContent(
            element,
            now
        ) {
            if (now === undefined) {
                now = new Date();
            }

            let timestamp = Colby.elementToTimestamp(
                element
            );

            if (
                timestamp === null
            ) {
                if (
                    element.hasAttribute(
                        "data-nulltextcontent"
                    )
                ) {
                    element.textContent = (
                        element.getAttribute("data-nulltextcontent")
                    );
                }

                return;
            }

            let date = new Date(
                timestamp
            );

            let args = {
                Colby_time_element_style: "Colby_time_element_style_default"
            };

            if (
                element.classList.contains(
                    "Colby_time_element_style_moment"
                )
            ) {
                args.Colby_time_element_style = "Colby_time_element_style_moment";
            } else if (
                element.classList.contains(
                    "compact"
                ) ||

                element.classList.contains(
                    "Colby_time_element_style_compact"
                )
            ) {
                args.Colby_time_element_style = "Colby_time_element_style_compact";
            }

            element.textContent = (
                Colby.dateToRelativeLocaleString(
                    date,
                    now,
                    args
                )
            );
        },
        /* updateCBTimeElementTextContent() */



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

    window.Colby =
    Colby;



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



    // -- functions



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
    function
    Colby_createTasksController(
    ) // -> object
    {
        let isStopped =
        false;

        let promise;

        let shared_tasksController;



        if (
            shared_tasksController !==
            undefined
        ) {
            return shared_tasksController;
        }

        shared_tasksController =
        {
            init:
            Colby_createTasksController_init,

            start:
            start,

            stop:
            stop,
        };

        return shared_tasksController;



        /**
         * @return undefined
         */
        function
        Colby_createTasksController_init(
        ) // -> undefined
        {
            if (
                !isStopped
            ) {
                start();
            }
        }
        // Colby_createTasksController_init()



        /**
         * Under normal circumstances the promise returned by this function is
         * not used. But there are some situations where the caller of this
         * function wants all tasks to run until they are complete, potentially
         * for a specific process ID, and the to have the promise resolve.
         *
         * The model import process is one of these situations.
         *
         * @return Promise -> undefined
         */
        function
        start(
        ) // -> Promise -> undefined
        {
            isStopped = false;

            if (promise) {
                return promise;
            }

            promise =
            new Promise(
                function (
                    resolve
                )
                {
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

                        CBAjax.call(
                            "CBTasks2",
                            "runNextTask",
                            args
                        ).then(
                            goAgainOrResolve
                        ).catch(
                            function (error) {
                                promise = undefined;

                                CBErrorHandler.report(
                                    error
                                );
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
    }
    // Colby_createTasksController()

}
)();

"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */

var Colby = {
    'intervalId' : null,
    'intervalCount' : 0,
    'monthNames' : ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'],

    /**
     * @param string text
     *
     * @return undefined
     */
    alert : function(text) {
        Colby.setPanelText(text);
        Colby.showPanel();
    },

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
     * @param Error error
     *
     * @return Promise (rejected)
     */
    displayError: function (error) {
        if (error.ajaxResponse) {
            Colby.displayResponse(error.ajaxResponse);
        } else {
            Colby.alert(error.message || "No error message was provided.");
        }

        return Promise.reject(error);
    },

    /**
     * @param ajaxResponse response
     *
     * @return undefined
     */
    displayResponse: function (response) {
        var element, message;
        if ('stackTrace' in response) {
            element                     = document.createElement("div");
            message                     = document.createElement("p");
            message.style.textAlign     = "center";
            message.style.marginBottom  = "100px";
            message.textContent         = response.message;
            var stack                   = document.createElement("pre");
            stack.style.fontSize        = "13px";
            stack.textContent           = response.stackTrace;

            element.appendChild(message);
            element.appendChild(stack);

            Colby.setPanelElement(element);
        } else if (response.userMustLogIn) {
            element                     = document.createElement("div");
            message                     = document.createElement("p");
            message.style.textAlign     = "center";
            message.textContent         = response.message;
            var button                  = document.createElement("button");
            button.textContent          = "Reload";
            button.style.display        = "block";
            button.style.margin         = "20px auto";

            button.addEventListener("click", function() { location.reload(); });

            element.appendChild(message);
            element.appendChild(button);

            Colby.setPanelElement(element);
        } else {
            Colby.setPanelText(response.message);
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
     * @deprecated CBTask replaced by CBTask2
     *
     * @return undefined
     */
    doTask: function () {
        var status = Colby.taskStatus;

        if (status === undefined) {
            status = {
                requestCount : 0,
                waiting : false,
            };

            Colby.taskStatus = status;
        }

        if (status.waiting) {
            return;
        }

        if (status.timeoutID) {
            window.clearTimeout(status.timeoutID);
            status.timeoutID = undefined;
        }

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.doTaskDidError.bind(undefined, {status:status,xhr:xhr});
        xhr.onload = Colby.doTaskDidLoad.bind(undefined, {status:status,xhr:xhr});
        xhr.open("POST", "/api/?class=CBTasks&function=doTask");
        xhr.send();

        status.requestCount += 1;
        status.waiting = true;
    },

    /**
     * @deprecated CBTask replaced by CBTask2
     *
     * @param object args.status
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    doTaskDidError: function (args) {
        args.status.waiting = false;
        args.status.error = true;
    },

    /**
     * @deprecated CBTask replaced by CBTask2
     *
     * @param object args.status
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    doTaskDidLoad: function (args) {
        args.status.waiting = false;
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.timeout !== undefined) {
            args.status.timeoutID = window.setTimeout(Colby.doTask, response.timeout);
        }
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
     * Makes an ajax request.
     *
     * @param string URL
     * @param ? data
     *  The data can be of any form accepted by the XMLHttpRequest.send()
     *  function. Most commonly, if used, it will be a FormData instance.
     *
     * @return Promise
     * Returns a promise that passes an 'ajax response' object (created by this
     * class) to resolve handlers. If an error occurs for any reason a
     * JavaScript Error object is passed to reject handlers with an 'ajax
     * response' object set to the Error's `ajaxResponse` propery.
     */
    fetchAjaxResponse: function(URL, data) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.onloadend = handler;
            xhr.open("POST", URL);
            xhr.send(data);

            function handler() {
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
     * @param string image.filename
     * @param string image.extension
     * @param hex160 image.ID
     * @param string? filename
     *      If specified, this will be used instead of the filename in the image
     *      parameter. Used to specify a smaller size such as "rw320" because
     *      often the image parameter will specify original.
     *
     * @return string
     */
    imageToURL : function (image, filename) {
        filename = filename || image.filename;
        var basename = filename + "." + image.extension;
        return "/" + Colby.dataStoreFlexpath(image.ID, basename);
    },

    /**
     * @return bool
     */
    localStorageIsSupported : function () {
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
     * Use this function with promises to report errors back to the server.
     *
     *      fetchData().catch(Colby.report)
     *
     * @param Error error
     *
     * @return Promise
     *  Returns a rejected promise the error parameter value.
     */
    report: function (error) {
        var column, line, message, pageURL, scriptURL;

        column = (error && error.column) ? error.column : "";
        line = (error && error.line) ? error.line : "";
        message = error ? error.toString() : "";
        pageURL = location.href;
        scriptURL = (error && error.sourceURL) ? error.sourceURL : "";

        var formData = new FormData();
        formData.append("columnNumber", column);
        formData.append("lineNumber", line);
        formData.append("message", message);
        formData.append("pageURL", pageURL);
        formData.append("scriptURL", scriptURL);

        // TODO: could insert a catch here for rare errors
        return Colby.fetchAjaxResponse("/colby/javascript-error/", formData).then(handle, handle);

        function handle() {
            return Promise.reject(error);
        }
    },

    /**
     * @return {
     *  message: string,
     *  wasSuccessful: bool,
     *  xhr: XMLHttpRequest,
     * }
     */
    responseFromXMLHttpRequest : function (xhr) {
        var response;

        switch (xhr.status) {
            case 0:
                response = {
                    className : "CBAjaxResponse",
                    message : "An Ajax request was aborted. This may be because of a network error making the server unavailable or because the Ajax request URL is incorrect.",
                    wasSuccessful : false,
                };
                break;
            case 200:
                response = JSON.parse(xhr.responseText);
                break;
            default:
                response = {
                    className : "CBAjaxResponse",
                    message : xhr.status + ' ' + xhr.statusText,
                    wasSuccessful : false,
                };
                break;
        }

        response.xhr = xhr;

        return response;
    },

    /**
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
     * @return undefined
     */
    updateTimes : function () {
        if (Colby.intervalId && Colby.intervalCount > 90) {
            // We only do updates every second for the first 90 seconds. It's 90 instead of 60 in case the server clock and the client clock are way off.

            clearInterval(Colby.intervalId);
            Colby.intervalId = null;

            // In the future only update the times every 15 seconds. We don't need to hold onto the interval id because we're never going to cancel it.

            setInterval(Colby.updateTimes, 15000);
        }

        Colby.intervalCount++;

        var args, date, dateString, element, timestamp;
        var elements = document.getElementsByClassName('time');
        var countOfElements = elements.length;
        var now = new Date();

        for (var i = 0; i < countOfElements; i++) {
            element = elements.item(i);
            timestamp = Colby.elementToTimestamp(element);

            if (timestamp === null) {
                if (element.hasAttribute("data-nulltextcontent")) {
                    element.textContent =
                        element.getAttribute("data-nulltextcontent");
                }

                continue;
            }

            date = new Date(timestamp);
            args = undefined;

            if (element.classList.contains("compact")) {
                args = { "compact" : true };
            }

            dateString = Colby.dateToRelativeLocaleString(date, now, args);
            element.textContent = dateString;
        }
    },

    /**
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
};

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
 * NOTE: Some errors will somehow disable this error handling. It's very odd.
 * When this happens use debugging to find the error and once you fix it the
 * error handling will start working again. I'm not sure exactly which errors
 * cause this strange behavior.
 *
 * BUG: 2016.12.28
 * Because this makes an asynchronous request it will not work if there is
 * navigation immediately after which cancels the request.
 *
 * @return undefined
 */
Colby.handleError = function(message, scriptURL, lineNumber, columnNumber, error) {
    var formData = new FormData();
    formData.append("message", message);
    formData.append("pageURL", location.href);
    formData.append("scriptURL", scriptURL || "");
    formData.append("lineNumber", lineNumber);

    var XHR = new XMLHttpRequest();
    XHR.open("POST", "/colby/javascript-error/");
    XHR.send(formData);

    return false;
};

/**
 * Set the error handler as soon as possible
 * to catch errors even if they occur later in this file.
 */
window.onerror = Colby.handleError;

/**
 * @return void
 */
Colby.beginUpdatingTimes = function()
{
    Colby.intervalCount = 0;

    if (!Colby.intervalId)
    {
        Colby.intervalId = setInterval(Colby.updateTimes, 1000);

        Colby.updateTimes();
    }
};

/**
 * Converts cents to dollars.
 *
 *      150 => "1.50"
 *      "5" => "0.05"
 *       75 => "0.75"
 *
 * @return string
 */
Colby.centsToDollars = function(cents)
{
    /**
     * Normalize cents to an integer. Parse as base 10.
     */

    cents = parseInt(cents, 10);

    /**
     * Convert to a string.
     */

    cents = String(cents);

    /**
     * Pad with zeros until the string is at least 3 digits long.
     */

    while (cents.length < 3)
    {
        cents = "0" + cents;
    }

    var dollars = cents.substr(0, cents.length - 2) + "." + cents.substr(-2);

    return dollars;
};

/**
 * @return null
 */
Colby.createPanel = function() {
    var panel = document.createElement("div");
    panel.className = "CBPanelView";
    var container = document.createElement("div");
    container.className = "CBPanelContainer";
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

/**
 * @return string
 */
Colby.dateToRelativeLocaleString = function(date, now, args) {
    if (args === undefined) args = {};
    var timespan = now.getTime() - date.getTime();
    var string;

    // date is in the future by more than 60 seconds
    if (timespan < (1000 * -60)) {
        string = Colby.dateToLocaleDateString(date) + ' ' + Colby.dateToLocaleTimeString(date);
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
    else if (timespan < (1000 * 60 * 60 * 24) && date.getDate() == now.getDate())
    {
        string = 'Today at ' + Colby.dateToLocaleTimeString(date, args);
    }

    // less than 48 house and yesterday
    else if (timespan < (1000 * 60 * 60 * 24 * 2) && ((date.getDay() + 1) % 7) == now.getDay())
    {
        string = 'Yesterday at ' + Colby.dateToLocaleTimeString(date, args);
    }

    // just return date and time
    else
    {
        string = Colby.dateToLocaleDateString(date, args) + ' ' + Colby.dateToLocaleTimeString(date, args);
    }

    return string;
};

/**
 * @return void
 */
Colby.handleContentLoaded = function()
{
    Colby.beginUpdatingTimes();
};

/**
 * @return void
 */
Colby.hidePanel = function()
{
    if (Colby.panel &&
        Colby.panel.parentNode)
    {
        Colby.panel.parentNode.removeChild(Colby.panel);
    }
};

/**
 * This method generates a random hex string representing a 160-bit number
 * which is the same length as a SHA-1 hash and can be used as a unique ID.
 *
 * @return string
 */
Colby.random160 = function()
{
    var i;
    var randomNumbers;

    if (typeof Uint16Array !== undefined &&
        window.crypto &&
        window.crypto.getRandomValues)
    {
        randomNumbers = new Uint16Array(10);

        window.crypto.getRandomValues(randomNumbers);
    }
    else
    {
        randomNumbers = [];

        for (i = 0; i < 10; i++)
        {
            var uint16 = Math.floor(Math.random() * 0xffff);

            randomNumbers.push(uint16);
        }
    }

    var random160 = "";

    for (i = 0; i < 10; i++)
    {
        var hex = randomNumbers[i].toString(16);

        hex = "0000".substr(0, 4 - hex.length) + hex;

        random160 = random160 + hex;
    }

    return random160;
};

/**
 * 2014.03.17
 *  This method has been deprecated.
 *
 * @return void
 */
Colby.setPanelContent = function(text)
{
    console.log("The `Colby.setPanelContent()` method has been deprecated.");

    var element     = document.createElement("pre");
    var textNode    = document.createTextNode(text);
    element.appendChild(textNode);

    Colby.setPanelElement(element);
};

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
 * @return string
 */
Colby.textToHTML = function(text)
{
    var html = String(text);

    html = html.replace(/&/g, "&amp;");
    html = html.replace(/</g, "&lt;");
    html = html.replace(/>/g, "&gt;");
    html = html.replace(/"/g, "&quot;");
    html = html.replace(/'/g, "&#039;");

    return html;
};

/**
 * @param string text
 *
 * @return string
 */
Colby.textToURI = function (text) {

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

Colby.updateTimestampForElementWithId = function(timestamp, id)
{
    var element = document.getElementById(id);

    element.setAttribute('data-timestamp', timestamp);

    Colby.beginUpdatingTimes();
};

(function() {
    var link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = "/colby/javascript/Colby.css";

    document.head.appendChild(link);
})();

/**
 * General page loaded tasks
 */

document.addEventListener('DOMContentLoaded', function () {
    Colby.beginUpdatingTimes();
    Colby.doTask(); // deprecated
});

/**
 * CBTasks2 dispatch
 */

Colby.CBTasks2DispatchAlways = false;
Colby.CBTasks2DispatchDelay = 5000;

document.addEventListener('DOMContentLoaded', function () {
    dispatch();

    function dispatch() {
        Colby.CBTasks2Promise =
            Colby.fetchAjaxResponse("/api/?class=CBTasks2&function=dispatchNextTask")
            .then(restart);
    }

    function restart(response) {
        if (Colby.CBTasks2DispatchAlways || response.taskWasDispatched) {
            setTimeout(dispatch, Colby.CBTasks2DispatchDelay);
        }
    }
});

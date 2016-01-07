"use strict";

var Colby = {
    'intervalId' : null,
    'intervalCount' : 0,
    'monthNames' : ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'],

    /**
     * @param {string} text
     *
     * @return undefined
     */
    alert : function(text) {
        Colby.setPanelText(text);
        Colby.showPanel();
    },

    dateToLocaleString : function (date) {
        return Colby.dateToLocaleDateString(date) + " " + Colby.dateToLocaleTimeString(date);
    },

    /**
     * @return undefined
     */
    displayResponse : function(response) {
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
     * @param Element element
     *
     * @return int|null
     */
    elementToTimestamp : function (element) {
        var timestamp = parseInt(element.getAttribute("data-timestamp"), 10);

        return isNaN(timestamp) ? null : timestamp;
    },

    /**
     * @return bool
     */
    localStorageIsSupported : function () {
        if (Colby.localStorageIsSupported === undefined) {
            var value = "value";

            try {
                localStorage.setItem(value, value);
                localStorage.removeItem(value);
                Colby.localStorageIsSupported = true;
            } catch(e) {
                Colby.localStorageIsSupported = false;
            }
        }

        return Colby.localStorageIsSupported;
    },

    /**
     * @return object
     */
    responseFromXMLHttpRequest : function(xhr) {
        var response;

        switch (xhr.status) {
            case 0:
                response = {
                    message : "An Ajax request was aborted. This may be because of a network error making the server unavailable or because the Ajax request URL is incorrect.",
                    wasSuccessful : false
                };
                break;

            case 200:
                response = JSON.parse(xhr.responseText);
                break;
            default:
                response = {
                    message         : xhr.status + ': ' + xhr.statusText,
                    wasSuccessful   : false
                };
                break;
        }

        return response;
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

    unixTimestampToParseableString : function (unixTimestamp) {
        return Colby.unixTimestampToParseableDateString(unixTimestamp) +
               " " +
               Colby.unixTimestampToParseableTimeString(unixTimestamp);
    },

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
 * This function is out of order to get the error handler set
 * as soon as possible.
 *
 * NOTE: Some errors will somehow disable this error handling. It's very odd.
 * When this happens use debugging to find the error and once you fix it the
 * error handling will start working again. I'm not sure exactly which errors
 * cause this strange behavior.
 *
 * @return void
 */
Colby.handleError = function(message, scriptURL, lineNumber, columnNumber, error)
{
    var formData = new FormData();
    formData.append("message",      message);
    formData.append("pageURL",      location.href);
    formData.append("scriptURL",    scriptURL ? scriptURL : "");
    formData.append("lineNumber",   lineNumber);

    var XHR = new XMLHttpRequest();
    XHR.open("POST", "/colby/javascript-error/", true);
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
 * @return string
 *  This method returns a date in the following format: "February 14, 2010".
 */
Colby.dateToLocaleDateString = function(date)
{
    /**
     * 2013.05.07
     *
     * Just before this comment was written this function used the
     * `toLocaleDateString` method because it was thought that it would
     * produce better localized results. However, mostly what it did was
     * produce unpredictable results on different browsers. Most of the output
     * was not even close to the preferred format that this method now
     * returns.
     *
     * I had an epiphany that, at least for now, all of the websites that
     * use Colby are in English anyway so having the goal of writing dates
     * using the locale was somewhat misguided, especially since it didn't
     * work anyway.
     *
     * If dates are needed in another language it will be a better approach to
     * just localize this method rather than relying on browsers to do it. If
     * the technology improves this decision can be revisited.
     */

    return Colby.monthNames[date.getMonth()] +
           ' ' +
           date.getDate() +
           ', ' + date.getFullYear();
};

/**
 * @return string
 */
Colby.dateToLocaleTimeString = function(date)
{
    var formattedHour = date.getHours() % 12;

    formattedHour = formattedHour ? formattedHour : 12;

    var formattedMinutes = date.getMinutes().toString();

    if (formattedMinutes.length < 2)
    {
        formattedMinutes = '0'.concat(formattedMinutes);
    }

    var formattedAMPM = (date.getHours() > 11) ? 'p.m.' : 'a.m.';

    return formattedHour +
    ':' +
    formattedMinutes +
    ' ' +
    formattedAMPM;
};

/**
 * @return string
 */
Colby.dateToRelativeLocaleString = function(date, now)
{
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
        string = 'Today at ' + Colby.dateToLocaleTimeString(date);
    }

    // less than 48 house and yesterday
    else if (timespan < (1000 * 60 * 60 * 24 * 2) && ((date.getDay() + 1) % 7) == now.getDay())
    {
        string = 'Yesterday at ' + Colby.dateToLocaleTimeString(date);
    }

    // just return date and time
    else
    {
        string = Colby.dateToLocaleDateString(date) + ' ' + Colby.dateToLocaleTimeString(date);
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
 * @return string
 */
Colby.textToURI = function(text)
{
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

/**
 * @return undefined
 */
Colby.updateTimes = function () {
    if (Colby.intervalId && Colby.intervalCount > 90) {
        // We only do updates every second for the first 90 seconds. It's 90 instead of 60 in case the server clock and the client clock are way off.

        clearInterval(Colby.intervalId);
        Colby.intervalId = null;

        // In the future only update the times every 15 seconds. We don't need to hold onto the interval id because we're never going to cancel it.

        setInterval(Colby.updateTimes, 15000);
    }

    Colby.intervalCount++;

    var date, element, timestamp;
    var elements = document.getElementsByClassName('time');
    var countOfElements = elements.length;
    var now = new Date();

    for (var i = 0; i < countOfElements; i++) {
        element = elements.item(i);
        timestamp = Colby.elementToTimestamp(element);

        if (timestamp === null) {
            if (element.hasAttribute("data-nulltextcontent")) {
                element.textContent = element.getAttribute("data-nulltextcontent");
            }

            continue;
        }

        date = new Date(timestamp);
        element.textContent = Colby.dateToRelativeLocaleString(date, now);
    }
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
    document.addEventListener('DOMContentLoaded', Colby.handleContentLoaded, false);
})();

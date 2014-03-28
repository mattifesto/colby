"use strict";

var Colby =
{
    'intervalId' : null,
    'intervalCount' : 0,
    'monthNames' : ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December']
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
Colby.handleError = function(message, url, lineNumber)
{
    console.log('message: ' + message + '\n' +
          'url: ' + url + '\n' +
          'line number: ' + lineNumber);
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
Colby.createPanel = function()
{
    var panel                   = document.createElement("div");
    panel.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    panel.style.bottom          = "0px";
    panel.style.left            = "0px";
    panel.style.position        = "fixed";
    panel.style.right           = "0px";
    panel.style.top             = "0px";

    var content                     = document.createElement("div");
    content.style.backgroundColor   = "white";
    content.style.color             = "black";
    content.style.margin            = "50px auto 0px";
    content.style.overflow          = "scroll";
    content.style.maxHeight         = "50%";
    content.style.padding           = "20px";
    content.style.width             = "720px";
    panel.appendChild(content);

    var buttonContainer                     = document.createElement("div");
    var button                              = document.createElement("button");
    var buttonText                          = document.createTextNode("Dismiss");
    buttonContainer.style.backgroundColor   = "white";
    buttonContainer.style.textAlign         = "center";
    buttonContainer.style.margin            = "0px auto";
    buttonContainer.style.padding           = "20px";
    buttonContainer.style.width             = "720px";
    buttonContainer.appendChild(button);
    button.addEventListener("click", Colby.hidePanel);
    button.appendChild(buttonText);
    panel.appendChild(buttonContainer);

    Colby.panel                 = panel;
    Colby.panelContent          = content;
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

    // less than 60 seconds
    if (timespan < (1000 * 60))
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
Colby.displayResponse = function(response)
{
    if ('stackTrace' in response)
    {
        var element     = document.createElement("pre");
        var textNode    = document.createTextNode(response.stackTrace);
        element.appendChild(textNode);

        Colby.setPanelElement(element);
    }
    else
    {
        Colby.setPanelText(response.message);
    }

    Colby.showPanel();
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
    var randomNumbers = new Uint16Array(10);

    crypto.getRandomValues(randomNumbers);

    var random160 = "";

    for (var i = 0; i < 10; i++)
    {
        var hex = randomNumbers[i].toString(16);

        hex = "0000".substr(0, 4 - hex.length) + hex;

        random160 = random160 + hex;
    };

    return random160;
};

/**
 * @return Object
 */
Colby.responseFromXMLHttpRequest = function(xhr)
{
    var response;

    if (xhr.status == 200)
    {
        response = JSON.parse(xhr.responseText);
    }
    else
    {
        response =
        {
            message         : xhr.status + ': ' + xhr.statusText,
            wasSuccessful   : false
        };
    }

    return response;
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
 * @return void
 */
Colby.setPanelElement = function(element)
{
    if (!Colby.panel)
    {
        Colby.createPanel();
    }

    while (Colby.panelContent.lastChild)
    {
        Colby.panelContent.removeChild(Colby.panelContent.lastChild);
    }

    Colby.panelContent.appendChild(element);
};

/**
 * @return void
 */
Colby.setPanelText = function(text)
{
    Colby.setPanelElement(document.createTextNode(text));
};

/**
 * @return void
 */
Colby.showPanel = function()
{
    if (!Colby.panel)
    {
        Colby.createPanel();
    }

    if (!Colby.panel.parentNode)
    {
        document.body.appendChild(Colby.panel);
    }
};

/**
 * @return string
 */
Colby.textToHTML = function(text)
{
    var html = String(text);

    html = html.replace(/&/g, "&amp;")
    html = html.replace(/</g, "&lt;")
    html = html.replace(/>/g, "&gt;")
    html = html.replace(/"/g, "&quot;")
    html = html.replace(/'/g, "&#039;");

    return html;
}

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
 * @return void
 */
Colby.updateTimes = function()
{
    if (Colby.intervalId && Colby.intervalCount > 90)
    {
        // We only do updates every second for the first 90 seconds. It's 90 instead of 60 in case the server clock and the client clock are way off.

        clearInterval(Colby.intervalId);
        Colby.intervalId = null;

        // In the future only update the times every 15 seconds. We don't need to hold onto the interval id because we're never going to cancel it.

        setInterval(Colby.updateTimes, 15000);
    }

    Colby.intervalCount++;

    var elements = document.getElementsByClassName('time');
    var countOfElements = elements.length;
    var now = new Date();

    for (var i = 0; i < countOfElements; i++)
    {
        var element = elements.item(i);
        var timestamp = +element.getAttribute('data-timestamp'); // Use + to convert to integer.

        if (!timestamp)
        {
            continue;
        }

        var date = new Date(timestamp);

        element.textContent = Colby.dateToRelativeLocaleString(date, now);
    }
};

Colby.updateTimestampForElementWithId = function(timestamp, id)
{
    var element = document.getElementById(id);

    element.setAttribute('data-timestamp', timestamp);

    Colby.beginUpdatingTimes();
};

if (document.addEventListener) // disable for IE8 and earlier
{
    document.addEventListener('DOMContentLoaded', Colby.handleContentLoaded, false);
}

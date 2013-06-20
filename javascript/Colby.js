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
    alert('message: ' + message + '\n' +
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
}

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
    var content;
    var classAttribute;

    if ('stackTrace' in response)
    {
        content = '<pre style="max-height: 300px; overflow-y: scroll; font-size: 12px;">' + response.stackTrace + '</pre>';
        classAttribute = 'large-panel';
    }
    else
    {
        content = '<div>' + response.message + '</div>';
        classAttribute = 'small-panel';
    }

    var html = ' \
<div class="' + classAttribute + '">' + content + ' \
    <div style="text-align: right;"> \
        <button onclick="ColbySheet.endSheet();">Dismiss</button> \
    </div> \
</div>';

    ColbySheet.beginSheet(html);
};

/**
 * @return void
 */
Colby.handleContentLoaded = function()
{
    Colby.beginUpdatingTimes();
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
 * @return string
 */
Colby.textToURI = function(text)
{
    /**
     * Convert all characters to lowercase to start the URI string.
     */

    var uri = text.toLowerCase();

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
}

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
}

if (document.addEventListener) // disable for IE8 and earlier
{
    document.addEventListener('DOMContentLoaded', Colby.handleContentLoaded, false);
}

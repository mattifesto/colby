"use strict";

var ColbyUnitTests =
{
};

ColbyUnitTests.alert = function(html)
{
    var sheetHTML = ' \
<div class="small-panel"> \
    <div>' + html + '</div> \
    <div style="text-align: right;"><button onclick="ColbySheet.endSheet();">Dismiss</button></div> \
</div>';

    ColbySheet.beginSheet(sheetHTML);
}

function doRunJavascriptUnitTests()
{
    var wasSuccessful = true;
    var now = new Date('2012/12/16 10:51 pm');
    var date = now;
    var tests = new Array();

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

    var countOfTests = tests.length;

    for (var i = 0; i < countOfTests; i++)
    {
        var string = Colby.dateToRelativeLocaleString(tests[i].date, now);

        if (string != tests[i].string)
        {
            ColbyUnitTests.errors += 'test failed\nexpected: "' + tests[i].string + '"\nreceived: "' + string + '"';

            wasSuccessful = false;

            break;
        }
    }

    // Report results

    if (wasSuccessful)
    {
        ColbyUnitTests.alert('Javascript unit tests passed.');
    }
    else
    {
        ColbyUnitTests.alert('Javascript unit tests failed.\n\n' + ColbyUnitTests.errors);
    }
}

function doRunUnitTests()
{
    beginAjax();

    var xhr = ColbyCreateXMLHttpRequest();
    xhr.open('POST', '/developer/test/ajax/run-unit-tests/', true);
    xhr.onload = handleAjaxResponse;
    xhr.send();
}

function handleAjaxResponse()
{
    if (this.status == 200)
    {
        var response = JSON.parse(this.responseText);
    }
    else
    {
        var response =
        {
            'message' : this.status + ': ' + this.statusText
        };
    }

    var errorLog = document.getElementById('error-log');

    // remove error-log element content

    while (errorLog.firstChild)
    {
        errorLog.removeChild(errorLog.firstChild);
    }

    var p = document.createElement('p');
    var t = document.createTextNode(response.message);

    p.appendChild(t);
    errorLog.appendChild(p);

    if ('stackTrace' in response)
    {
        var pre = document.createElement('pre');
        t = document.createTextNode(response.stackTrace);

        pre.appendChild(t);
        errorLog.appendChild(pre);
    }

    endAjax();
}

function beginAjax()
{
    var progress = document.getElementById('ajax-communication');

    progress.removeAttribute('value');
}

function endAjax()
{
    var progress = document.getElementById('ajax-communication');

    progress.setAttribute('value', '0');
}

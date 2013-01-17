<?php

$page = ColbyOutputManager::beginVerifiedUserPage(
    'Unit Tests', 'Develeloper tests to make sure there are no regressions in functionality.', 'admin');

?>

<style>
input[type=text]
{
    width: 400px;
    padding: 2px;
}

dd
{
    margin: 5px 0px 15px;
}
</style>

<?php

if (!COLBY_MYSQL_HOST)
{
    ?>

    <p>Please finish setting up the colby-configuration.php file and return to this page.

    <?php
}
else
{
    ?>

    <p>This file was loaded: <span class="time" data-timestamp="<?php echo time() * 1000; ?>"></span>
    <p><button onclick="doRunUnitTests();">Run unit tests</button>
    <progress id="ajax-communication" value="0"></progress>

    <p><button onclick="doRunJavascriptUnitTests();">Run Javascript unit tests</button>

    <?php
}

?>

<div id="error-log"></div>
<script>

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

var xhr;

function doRunJavascriptUnitTests()
{
    var wasSuccessful = true;
    var now = new Date('2012/12/16 10:51 pm');
    var date = now;
    var tests = new Array();

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
        'string' : 'Today at 9:51 PM'
    });

    tests.push({
        'date' : new Date('2012/12/16 12:00 am'),
        'string' : 'Today at 12:00 AM'
    });

    tests.push({
        'date' : new Date('2012/12/15 11:59 pm'),
        'string' : 'Yesterday at 11:59 PM'
    });

    tests.push({
        'date' : new Date('2012/12/15 12:00 am'),
        'string' : 'Yesterday at 12:00 AM'
    });

    tests.push({
        'date' : new Date('2012/12/14 1:53 pm'),
        'string' : '2012/12/14 1:53 PM'
    });

    // Run tests

    var countOfTests = tests.length;

    for (var i = 0; i < countOfTests; i++)
    {
        var string = Colby.dateToRelativeLocaleString(tests[i].date, now);

        if (string != tests[i].string)
        {
            ColbyUnitTests.alert('test failed\nexpected: "' + tests[i].string + '"\nreceived: "' + string + '"');

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
        ColbyUnitTests.alert('Javascript unit tests failed.');
    }
}

function doRunUnitTests()
{
    beginAjax();

    xhr = new ColbyXMLHttpRequest();
    xhr.open('POST', '/developer/test/ajax/run-unit-tests/', true);
    xhr.onload = handleAjaxResponse;
    xhr.send();

    xhr = xhr.request; // Remove this line when moving to direct use of XMLHttpRequest.
}

function handleAjaxResponse()
{
    if (xhr.status == 200)
    {
        var response = JSON.parse(xhr.responseText);
    }
    else
    {
        var response =
        {
            'message' : xhr.status + ': ' + xhr.statusText
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

    xhr = null;

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

</script>

<?php

$page->end();


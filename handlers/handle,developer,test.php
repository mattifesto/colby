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

    <button onclick="doRunUnitTests();">Run Unit Tests</button>
    <progress id="ajax-communication" value="0"></progress>

    <?php
}

?>

<div id="error-log"></div>
<script>

"use strict";

var xhr;

function doRunUnitTests()
{
    beginAjax();

    xhr = new XMLHttpRequest();
    xhr.open('POST', '/developer/test/ajax/run-unit-tests/', true);
    xhr.onload = handleAjaxResponse;
    xhr.send();
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


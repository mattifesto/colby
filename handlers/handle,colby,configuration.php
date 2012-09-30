<?php

$args = new stdClass();
$args->title = 'Colby Configuration';
$args->description = 'Use this page to configure Colby.';

ColbyPage::beginAdmin($args);

?>

<style>
h1
{
    margin-bottom: 1.0em;
}

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

<h1>Colby Configuration</h1>

<!--
<dl>
    <dt>Site URL</dt>
    <dd><input type="text" id="colby-site-url" value="http://<?php echo $_SERVER['SERVER_NAME']; ?>"></dd>
    <dt>Site Name</dt>
    <dd><input type="text" id="colby-site-name" value=""></dd>
    <dt>Site Administrator Email Address</dt>
    <dd><input type="text" id="colby-site-administrator" value=""></dd>
    <dt>Facebook App ID</dt>
    <dd><input type="text" id="colby-facebook-app-id" value=""></dd>
    <dt>Facebook App Secret</dt>
    <dd><input type="text" id="colby-facebook-app-secret" value=""></dd>
    <dt>First Verified User Facebook ID</dt>
    <dd><input type="text" id="colby-facebook-first-verified-user-id" value=""></dd>
    <dt>MySQL Host</dt>
    <dd><input type="text" id="colby-mysql-host" value=""></dd>
    <dt>MySQL Database</dt>
    <dd><input type="text" id="colby-mysql-database" value=""></dd>
    <dt>MySQL User</dt>
    <dd><input type="text" id="colby-mysql-user" value=""></dd>
    <dt>MySQL Password</dt>
    <dd><input type="text" id="colby-mysql-password" value=""></dd>
    <dt>Developer Information</dt>
    <dd><input type="checkbox" id="colby-site-is-being-debugged"> site is being debugged</dd>
</dl>
-->

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

    <button onclick="doInstallColby();">Install Colby</button>
    <progress id="ajax-communication" value="0"></progress>

    <?php
}

?>

<div id="error-log"></div>
<script>

"use strict";

var xhr;

function doInstallColby()
{
    beginAjax();

    xhr = new XMLHttpRequest();
    xhr.open('POST', '/colby/ajax/setup-database/', true);
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

ColbyPage::end();


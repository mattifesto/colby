<?php

ColbyPage::requireVerifiedUser();

if (isset($_GET['archive-id']))
{
    $archiveId = $_GET['archive-id'];

    $archive = ColbyArchive::open($archiveId);

    if ($archive->attributes()->created)
    {
        $hasData = true;
        $data = $archive->rootObject();
    }
    else
    {
        $hasData = false;
    }
}
else
{
    $archiveId = sha1(microtime() . rand() . ColbyUser::currentUserId());

    header("Location: /admin/blog/d74e2f3d347395acdb627e7c57516c3c4c94e988/edit/?archive-id={$archiveId}");
}

$args = new stdClass();
$args->title = 'Generic Blog Post Editor';
$args->description = 'Create and edit generic blog posts.';

ColbyPage::beginAdmin($args);

?>

<style>
input,
textarea
{
    display: block;
    width: 100%;
    padding: 4px;
    margin-top: 5px;
    border: 1px solid LightGray;
}

fieldset
{
    width: 600px;
    border: none;
}

fieldset > div + div
{
    margin-top: 10px;
}
</style>

<h1>Generic Blog Post Editor</h1>

<p><progress id="ajax-communication" value="0"></progress>

<fieldset>
    <div>Title <input type="text"
                      id="title"
                      class="form-field"
                      value="<?php if ($hasData) echo ColbyConvert::textToHTML($data->title); ?>"
                      onkeydown="handleKeyDown(this);"></div>
    <div>Stub <input type="text"
                     id="stub"
                     class="form-field"
                     value="<?php if ($hasData) echo ColbyConvert::textToHTML($data->stub); ?>"
                     readonly="readonly"
                     onkeydown="handleKeyDown(this);"></div>
    <div>Content <textarea id="content"
                           class="form-field"
                           style="height: 400px;"
                           onkeydown="handleKeyDown(this);"><?php

        if ($hasData) echo ColbyConvert::textToHTML($data->content);

    ?></textarea></div>
</fieldset>

<div id="error-log"></div>

<script>
"use strict";

var archiveId = '<?php echo $archiveId; ?>';
var needsUpdate = false;
var isUpdating = false;
var timer = null;
var xhr = null;

/**
 * @return object
 *  ajax response data
 */
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
            'wasSuccessful' : false,
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

    return response;
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

function handleKeyDown(sender)
{
    if (sender)
    {
        sender.style.backgroundColor = 'LightYellow';
    }

    needsUpdate = true;

    if (!isUpdating)
    {
        if (timer)
        {
            clearTimeout(timer);
        }

        timer = setTimeout(updateBlogPost, 2000);
    }
}

function updateBlogPost()
{
    isUpdating = true;
    needsUpdate = false;

    beginAjax();

    var content = document.getElementById('content');
    var stub = document.getElementById('stub');
    var title = document.getElementById('title');

    var formData = new FormData();
    formData.append('archive-id', archiveId);
    formData.append('content', content.value);
    formData.append('stub', content.value);
    formData.append('title', title.value);

    xhr = new XMLHttpRequest();
    xhr.open('POST', '/admin/blog/d74e2f3d347395acdb627e7c57516c3c4c94e988/ajax/update/', true);
    xhr.onload = handleBlogPostUpdated;
    xhr.send(formData);
}

function handleBlogPostUpdated()
{
    isUpdating = false;

    var response = handleAjaxResponse();

    if (response.wasSuccessful)
    {
        document.getElementById('stub').value = response.stub;
    }

    if (needsUpdate)
    {
        handleKeyDown(null);
    }
    else
    {
        var elements = document.getElementsByClassName('form-field');

        for (var i = 0; i < elements.length; i++)
        {
            elements[i].style.backgroundColor = 'Transparent';
        }
    }
}
</script>

<?php

ColbyPage::end();

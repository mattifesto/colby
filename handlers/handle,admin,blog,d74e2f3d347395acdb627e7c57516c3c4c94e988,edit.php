<?php

ColbyPage::requireVerifiedUser();

if (isset($_GET['archive-id']))
{
    $archiveId = $_GET['archive-id'];

    $archive = ColbyArchive::open($archiveId);

    if ($archive->attributes()->created)
    {
        $data = $archive->rootObject();
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
input[type=text],
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

    <div><label>Title
        <input type="text"
               id="title"
               class="form-field"
               value="<?php if (isset($data->titleHTML)) echo $data->titleHTML; ?>"
               onkeydown="handleValueChanged(this);">
    </label></div>

    <div><label>Stub
        <input type="text"
               id="stub"
               class="form-field"
               value="<?php if (isset($data->stub)) echo $data->stub; ?>"
               readonly="readonly"
               onkeydown="handleValueChanged(this);">
    </label></div>

    <div><label>Subhead
        <input type="text"
               id="subhead"
               class="form-field"
               value="<?php if (isset($data->subheadHTML)) echo $data->subheadHTML; ?>"
               onkeydown="handleValueChanged(this);">
    </label></div>

    <div><label>Content
        <textarea id="content"
                  class="form-field"
                   style="height: 400px;"
                 onkeydown="handleValueChanged(this);"><?php

        if (isset($data->content)) echo ColbyConvert::textToHTML($data->content);

        ?></textarea>
    </label></div>

    <div><label>
        <input type="checkbox"
               id="published"
               <?php if (isset($data->published)) echo 'checked="checked"'; ?>
               onclick="handleValueChanged(this);">
    Published</label><span style="float:right;"><?php

        if (isset($data->publishedAt))
        {
            // TODO: echo publication date
        }
        else
        {
            echo 'publication date will be set when first published';
        }

    ?></span></div>

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

function handleValueChanged(sender)
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

    var title = document.getElementById('title');
    var stub = document.getElementById('stub');
    var subhead = document.getElementById('subhead');
    var content = document.getElementById('content');

    var formData = new FormData();
    formData.append('archive-id', archiveId);
    formData.append('title', title.value);
    formData.append('stub', stub.value);
    formData.append('subhead', subhead.value);
    formData.append('content', content.value);

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
        handleValueChanged(null);
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

<?php

ColbyPage::requireVerifiedUser();

include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyGenericBlogPost.php');

if (isset($_GET['archive-id']))
{
    $archiveId = $_GET['archive-id'];

    $archive = ColbyArchive::open($archiveId);

    if ($archive->attributes()->created)
    {
        $data = $archive->rootObject();
    }
    else
    {
        $data = new ColbyGenericBlogPost();
    }
}
else
{
    $archiveId = sha1(microtime() . rand() . ColbyUser::currentUserId());

    header("Location: /admin/blog/generic-post/edit/?archive-id={$archiveId}");
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
                      value="<?php echo ColbyConvert::textToHTML($data->title); ?>"
                      onkeypress="handleKeyPressed(this);"></div>
    <div>Stub <input type="text"
                     id="stub"
                     class="form-field"
                     value="<?php echo ColbyConvert::textToHTML($data->stub); ?>"
                     readonly="readonly"
                     onkeypress="handleKeyPressed(this);"></div>
    <div>Content <textarea id="content"
                           class="form-field"
                           style="height: 400px;"
                           onkeypress="handleKeyPressed(this);"><?php

        echo ColbyConvert::textToHTML($data->content);

    ?></textarea></div>
</fieldset>

<div id="error-log"></div>

<script>
"use strict";

var archiveId = '<?php echo $archiveId; ?>';
var needsUpdate = false;
var isUpdating = false;
var timer = null;
var xhr;

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

function handleKeyPressed(sender)
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
    xhr.open('POST', '/admin/blog/generic-post/ajax/update/', true);
    xhr.onload = handleBlogPostUpdated;
    xhr.send(formData);
}

function handleBlogPostUpdated()
{
    isUpdating = false;

    handleAjaxResponse();

    if (needsUpdate)
    {
        handleKeyPressed(null);
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

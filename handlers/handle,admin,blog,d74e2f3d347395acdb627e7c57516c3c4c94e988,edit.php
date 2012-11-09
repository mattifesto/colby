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

// mise en place

$title = isset($data->titleHTML) ? $data->titleHTML : '';
$subtitle = isset($data->subtitleHTML) ? $data->subtitleHTML : '';
$stub = isset($data->stub) ? $data->stub : '';
$stubIsLocked = (isset($data->stubIsLocked) && $data->stubIsLocked) ? ' checked="checked"' : '';
$content = isset($data->content) ? ColbyConvert::textToHTML($data->content) : '';
$isPublished = isset($data->published) ? ' checked="checked"' : '';

$javascriptPublished = isset($data->published) ? $data->published * 1000 : 'null';
$javascriptPublicationDate = isset($data->publicationDate) ? $data->publicationDate * 1000 : 'null';

// begin page

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
               value="<?php echo $title; ?>"
               onkeydown="handleValueChanged(this);">
    </label></div>

    <div><label>Subtitle
        <input type="text"
               id="subtitle"
               class="form-field"
               value="<?php echo $subtitle; ?>"
               onkeydown="handleValueChanged(this);">
    </label></div>

    <div>
        <label style="float:right;"><input type="checkbox"<?php echo $stubIsLocked; ?>> Locked</label>
        <label>Stub
            <input type="text"
                   id="stub"
                   class="form-field"
                   value="<?php echo $stub; ?>"
                   readonly="readonly"
                   onkeydown="handleValueChanged(this);">
        </label>
    </div>

    <div><label>Content
        <textarea id="content"
                  class="form-field"
                   style="height: 400px;"
                 onkeydown="handleValueChanged(this);"><?php echo $content; ?></textarea>
    </label></div>

    <div>
        <label style="float: right;">
            <input type="checkbox"
                   id="is-published"
                   <?php echo $isPublished; ?>
                   onclick="handlePublishedChanged(this);">
        Published</label>
        <label>Publication Date:
            <input type="text"
                   id="publication-date"
                   class="form-field"
                   onblur="handlePublicationDateBlurred(this);">
        </label>
    </div>

</fieldset>
<div id="error-log"></div>

<script>
"use strict";

var archiveId = '<?php echo $archiveId; ?>';
var published = <?php echo $javascriptPublished; ?>;
var publicationDate = <?php echo $javascriptPublicationDate; ?>;

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

function handleContentLoaded()
{
    if (publicationDate)
    {
        var date = new Date(publicationDate);

        document.getElementById('publication-date').value = date.toLocaleString();
    }
}

function handlePublicationDateBlurred(sender)
{
    var date = new Date(Date.parse(sender.value));

    if (isNaN(date))
    {
        alert('Can\'t parse date value "' + sender.value + '".');

        return;
    }

    var timestamp = date.getTime();

    if (publicationDate != timestamp)
    {
        publicationDate = timestamp;

        if (published)
        {
            published = timestamp;
        }

        sender.value = date.toLocaleString();

        handleValueChanged(sender);
    }
}

function handlePublishedChanged(sender)
{
    if (sender.checked)
    {
        if (publicationDate === null)
        {
            var date = new Date();

            publicationDate = date.getTime();

            document.getElementById('publication-date').value = date.toLocaleString();
        }

        published = publicationDate;
    }
    else
    {
        published = null;
    }

    handleValueChanged(null);
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
    var subtitle = document.getElementById('subtitle');
    var stub = document.getElementById('stub');
    var content = document.getElementById('content');

    var phpPublished = published ? Math.floor(published / 1000) : '';
    var phpPublicationDate = publicationDate ? Math.floor(publicationDate / 1000) : '';

    var formData = new FormData();
    formData.append('archive-id', archiveId);
    formData.append('title', title.value);
    formData.append('subtitle', subtitle.value);
    formData.append('stub', stub.value);
    formData.append('content', content.value);
    formData.append('published', phpPublished);
    formData.append('publication-date', phpPublicationDate);

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

document.addEventListener('DOMContentLoaded', handleContentLoaded, false);

</script>

<?php

ColbyPage::end();

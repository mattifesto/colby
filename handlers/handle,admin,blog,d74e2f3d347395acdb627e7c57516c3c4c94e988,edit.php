<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Generic Blog Post Editor',
                                                  'Create and edit generic blog posts.',
                                                  'admin');

if (!isset($_GET['archive-id']))
{
    $archiveId = sha1(microtime() . rand());

    header("Location: {$_SERVER['REQUEST_URI']}?archive-id={$archiveId}");
}

$archiveId = $_GET['archive-id'];

$archive = ColbyArchive::open($archiveId);

if ($archive->attributes()->created)
{
    $data = $archive->rootObject();
}

// mise en place

$ajaxURL = COLBY_SITE_URL . '/admin/blog/d74e2f3d347395acdb627e7c57516c3c4c94e988/ajax/update/';

$published = isset($data->published) ? $data->published : '';
$publicationDate = isset($data->publicationDate) ? $data->publicationDate : '';
$title = isset($data->titleHTML) ? $data->titleHTML : '';
$subtitle = isset($data->subtitleHTML) ? $data->subtitleHTML : '';
$stub = isset($data->stub) ? $data->stub : '';
$stubIsLocked = (isset($data->stubIsLocked) && $data->stubIsLocked) ? ' checked="checked"' : '';
$content = isset($data->content) ? ColbyConvert::textToHTML($data->content) : '';
$isPublished = isset($data->published) ? ' checked="checked"' : '';

$javascriptPublished = isset($data->published) ? $data->published * 1000 : 'null';
$javascriptPublicationDate = isset($data->publicationDate) ? $data->publicationDate * 1000 : 'null';

?>

<fieldset>
    <input type="hidden" id="archive-id" value="<?php echo $archiveId; ?>">
    <input type="hidden" id="published" value="<?php echo $published; ?>">
    <input type="hidden" id="publication-date" value="<?php echo $publicationDate; ?>">

    <progress value="0"
              style="width: 100px; float: right;"></progress>

    <div><label>Title
        <input type="text"
               id="title"
               value="<?php echo $title; ?>">
    </label></div>

    <div><label>Subtitle
        <input type="text"
               id="subtitle"
               value="<?php echo $subtitle; ?>">
    </label></div>

    <div>
        <label style="float:right;"><input type="checkbox"<?php echo $stubIsLocked; ?>> Locked</label>
        <label>Stub
            <input type="text"
                   id="stub"
                   value="<?php echo $stub; ?>"
                   readonly="readonly">
        </label>
    </div>

    <div><label>Content
        <textarea id="content"
                  style="height: 400px;"><?php echo $content; ?></textarea>
    </label></div>

    <div>
        <label style="float: right;">
            <input type="checkbox"
                   class="ignore"
                   <?php echo $isPublished; ?>
                   onclick="handlePublishedChanged(this);">
        Published</label>
        <label>Publication Date:
            <input type="text"
                   id="publication-date-text"
                   class="ignore"
                   onblur="handlePublicationDateBlurred(this);">
        </label>
    </div>

</fieldset>

<div id="error-log"></div>

<script>
"use strict";

var formManager = null;

var published = <?php echo $javascriptPublished; ?>;
var publicationDate = <?php echo $javascriptPublicationDate; ?>;

function handleContentLoaded()
{
    // TODO: Make absolute URL

    formManager = new ColbyFormManager('<?php echo $ajaxURL; ?>');

    if (publicationDate)
    {
        var date = new Date(publicationDate);

        document.getElementById('publication-date-text').value = date.toLocaleString();
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

        var phpPublicationDate = Math.floor(publicationDate / 1000);

        document.getElementById('publication-date').value = phpPublicationDate;
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

            document.getElementById('publication-date-text').value = date.toLocaleString();
        }

        published = publicationDate;
    }
    else
    {
        published = null;
    }

    var phpPublished = published ? Math.floor(published / 1000) : '';

    document.getElementById('published').value = phpPublished;
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

$page->end();

<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Generic Document Editor',
                                                  'Create and edit generic documents.',
                                                  'admin');

if (!isset($_GET['archive-id']))
{
    $archiveId = sha1(microtime() . rand());

    header("Location: {$_SERVER['REQUEST_URI']}?archive-id={$archiveId}");
}

$archiveId = $_GET['archive-id'];
$groupId  = isset($_GET['group-id']) ? $_GET['group-id'] : '';
$groupStub  = isset($_GET['group-stub']) ? $_GET['group-stub'] : '';

$archive = ColbyArchive::open($archiveId);

if ($archive->attributes()->created)
{
    $data = $archive->rootObject();
}

// mise en place

$ajaxURL = COLBY_SITE_URL . '/admin/model/d74e2f3d347395acdb627e7c57516c3c4c94e988/ajax/update/';

$published = isset($data->published) ? $data->published : '';
$publicationDate = isset($data->publicationDate) ? $data->publicationDate : '';
$title = isset($data->titleHTML) ? $data->titleHTML : '';
$subtitle = isset($data->subtitleHTML) ? $data->subtitleHTML : '';
$stub = isset($data->stub) ? $data->stub : '';
$stubIsLocked = (isset($data->stubIsLocked) && $data->stubIsLocked) ? ' checked="checked"' : '';
$stubIsCustom = (isset($data->stubIsCustom) && $data->stubIsCustom) ? ' checked="checked"' : '';
$content = isset($data->content) ? ColbyConvert::textToHTML($data->content) : '';
$isPublished = isset($data->published) ? ' checked="checked"' : '';

$javascriptPublished = isset($data->published) ? $data->published * 1000 : 'null';
$javascriptPublicationDate = isset($data->publicationDate) ? $data->publicationDate * 1000 : 'null';

?>

<fieldset>
    <input type="hidden" id="archive-id" value="<?php echo $archiveId; ?>">
    <input type="hidden" id="group-id" value="<?php echo $groupId; ?>">
    <input type="hidden" id="group-stub" value="<?php echo $groupStub; ?>">
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
        <label style="float:right; margin-left: 20px;">
            <input type="checkbox"
                   id="stub-is-locked"
                   <?php echo $stubIsLocked; ?>> Locked
        </label>
        <label style="float:right;">
            <input type="checkbox"
                   id="stub-is-custom"
                   <?php echo $stubIsCustom; ?>> Custom
        </label>
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
                   <?php echo $isPublished; ?>
                   onchange="handlePublishedChanged(this);">
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

/**
 * @return void
 */
function handleContentLoaded()
{
    formManager = new ColbyFormManager('<?php echo $ajaxURL; ?>');

    formManager.updateCompleteCallback = updateCompleteCallback;

    if (publicationDate)
    {
        var date = new Date(publicationDate);

        document.getElementById('publication-date-text').value = date.toLocaleString();
    }

    if (published)
    {
        document.getElementById('stub-is-custom').disabled = true;
        document.getElementById('stub-is-locked').disabled = true;
    }
}

/**
 * @return void
 */
function handlePublicationDateBlurred(sender)
{
    var date = new Date(sender.value);

    if (isNaN(date))
    {
        // TODO: turn field red by adding a class
        // remove it when field is successfully set

        alert('Can\'t parse date value "' + sender.value + '".');

        return;
    }

    setPublicationDate(date.getTime());
}

/**
 * @return void
 */
function handlePublishedChanged(sender)
{
    if (sender.checked)
    {
        if (publicationDate === null)
        {
            setPublicationDate(new Date().getTime());
        }

        published = publicationDate;

        // When a post is published the stub is always automatically locked to maintain the permalink. If the user unpublishes the post they have the choice to unlock the stub if desired.

        var locked = document.getElementById('stub-is-locked');

        if (!locked.checked)
        {
            locked.checked = true;

            // Simulate the user checking the checkbox indicate the state has changed.

            var event = document.createEvent('Event');

            event.initEvent('change', false, false);

            locked.dispatchEvent(event);
        }

        document.getElementById('stub-is-custom').disabled = true;
        document.getElementById('stub-is-locked').disabled = true;
    }
    else
    {
        published = null;

        document.getElementById('stub-is-custom').disabled = false;
        document.getElementById('stub-is-locked').disabled = false;
    }

    var phpPublished = published ? Math.floor(published / 1000) : '';

    document.getElementById('published').value = phpPublished;

    sender.parentNode.classList.add('needs-update');
}

/**
 * @return void
 */
function setPublicationDate(timestamp)
{
    if (publicationDate == timestamp)
    {
        return;
    }

    publicationDate = timestamp;
    var date = new Date(timestamp);

    var publicationDateTextElement = document.getElementById('publication-date-text');

    publicationDateTextElement.value = date.toLocaleString();
    publicationDateTextElement.classList.add('needs-update');

    var phpPublicationDate = Math.floor(publicationDate / 1000);

    if (published)
    {
        published = timestamp;
        document.getElementById('published').value = phpPublicationDate;
    }

    document.getElementById('publication-date').value = phpPublicationDate;

    formManager.setNeedsUpdate(true);
}

/**
 * @return void
 */
function updateCompleteCallback(response)
{
    if (!response.wasSuccessful)
    {
        return;
    }

    var locked = document.getElementById('stub-is-locked');

    if (locked.checked)
    {
        return;
    }

    var custom = document.getElementById('stub-is-custom');

    if (custom.checked)
    {
        return;
    }

    var stub = document.getElementById('stub');

    if (stub.value != response.suggestedStub)
    {
        stub.value = response.suggestedStub;

        // Changing the value of the stub input will not notify the system that the stub has changed. We could just call setNeedsUpdate(true) on the formManager but instead we send the 'input' event to the stub input element which will highlight it, giving the user visual feedback of the change, and also notify the formManager that an update is needed.

        var event = document.createEvent('Event');

        event.initEvent('input', false, false);

        stub.dispatchEvent(event);
    }
}

document.addEventListener('DOMContentLoaded', handleContentLoaded, false);

</script>

<?php

$page->end();

<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Generic Document Editor',
                                                  'Create and edit generic documents.',
                                                  'admin');

$archiveId = isset($_GET['archive-id']) ? $_GET['archive-id'] : '';

if (empty($archiveId))
{
    $archiveId = sha1(microtime() . rand());

    $parts = explode('?', $_SERVER['REQUEST_URI']);

    $path = $parts[0];

    $queryString = isset($parts[1]) ? $parts[1] : '';
    $queryString = "archive-id={$archiveId}&{$queryString}";

    header("Location: {$path}?{$queryString}");
}

$archive = ColbyArchive::open($archiveId);

if ($archive->attributes()->created)
{
    $data = $archive->rootObject();

    $viewId = $data->viewId;
}
else
{
    $viewId = $_GET['view-id'];

    $data = ColbyPage::pageWithViewId($viewId);
}

// mise en place

$ajaxURL = COLBY_SITE_URL . '/admin/model/d74e2f3d347395acdb627e7c57516c3c4c94e988/ajax/update/';

$publicationDate = $data->publicationDate;
$title = $data->titleHTML;
$subtitle = $data->subtitleHTML;

$customPageStubText = $data->customPageStubText;
$groupStub = $data->groupStub;
$preferredStub = $data->preferredStub();
$preferredPageStub = $data->preferredPageStub;
$stub = $data->stub();
$stubIsLocked = $data->stubIsLocked ? ' checked="checked"' : '';

$content = isset($data->content) ? ColbyConvert::textToHTML($data->content) : '';

$isPublished = $data->isPublished ? ' checked="checked"' : '';
$publishedBy = $data->publishedBy;
$currentUserId = ColbyUser::currentUserId();

$javascriptPublicationDate = isset($data->publicationDate) ? $data->publicationDate * 1000 : 'null';

?>

<fieldset>
    <input type="hidden" id="archive-id" value="<?php echo $archiveId; ?>">
    <input type="hidden" id="view-id" value="<?php echo $viewId; ?>">
    <input type="hidden" id="preferred-page-stub" value="<?php echo $preferredPageStub; ?>">
    <input type="hidden" id="published-by" value="<?php echo $publishedBy; ?>">
    <input type="hidden" id="publication-date" value="<?php echo $publicationDate; ?>">

    <progress value="0"
              style="width: 100px; float: right;"></progress>

    <div><label>Title
        <input type="text"
               id="title"
               value="<?php echo $title; ?>">
    </label></div>

    <div style="padding: 0px 50px; font-size: 0.75em;">
        <style scoped="scoped">
            .stub
            {
                font-family: "Courier New", monospace;
            }

            table.stubs
            {
                width: 100%;
                margin-bottom: 5px;
            }

            table.stubs tr td:first-child
            {
                width: 100px;
                text-align: right;
            }
        </style>
        <table class="stubs"><tr>
            <td>Preferred URL:</td>
            <td id="preferred-stub-view" class="stub"><?php echo $preferredStub; ?></td>
        </tr><tr>
            <td>Actual URL:</td>
            <td id="stub-view" class="stub"><?php echo $stub; ?></td>
        </td></table>
        <label style="float:right; margin-left: 20px;">
            <input type="checkbox"
                   id="stub-is-locked"
                   <?php echo $stubIsLocked; ?>> Lock Stub
        </label>
        <label>Custom Stub Text
            <input type="text"
                   id="custom-page-stub-text"
                   value="<?php echo $customPageStubText; ?>">
        </label>
    </div>

    <div><label>Subtitle
        <input type="text"
               id="subtitle"
               value="<?php echo $subtitle; ?>">
    </label></div>

    <div><label>Content
        <textarea id="content"
                  style="height: 400px;"><?php echo $content; ?></textarea>
    </label></div>

    <div>
        <label style="float: right;">
            <input type="checkbox"
                   id="is-published"
                   <?php echo $isPublished; ?>
                   onchange="handleIsPublishedChanged(this);">
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

var currentUserId = <?php echo $currentUserId; ?>;
var groupStub = '<?php echo $groupStub; ?>';
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

    if (document.getElementById('is-published').checked)
    {
        document.getElementById('custom-page-stub-text').disabled = true;
        document.getElementById('stub-is-locked').disabled = true;
    }

    document.getElementById('title').addEventListener('input', updatePreferredPageStub, false);
    document.getElementById('custom-page-stub-text').addEventListener('input', updatePreferredPageStub, false);
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
function handleIsPublishedChanged(sender)
{
    if (sender.checked)
    {
        if (publicationDate === null)
        {
            setPublicationDate(new Date().getTime());
        }

        var publishedBy = document.getElementById('published-by');

        if (!publishedBy.value)
        {
            publishedBy.value = currentUserId;
        }

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

        document.getElementById('custom-page-stub-text').disabled = true;
        document.getElementById('stub-is-locked').disabled = true;
    }
    else
    {
        document.getElementById('custom-page-stub-text').disabled = false;
        document.getElementById('stub-is-locked').disabled = false;
    }
}

/**
 * @return void
 */
function updatePreferredPageStub()
{
    if (document.getElementById('stub-is-locked').checked)
    {
        return;
    }

    var stubText = document.getElementById('custom-page-stub-text').value.trim();

    if (!stubText)
    {
        stubText = document.getElementById('title').value.trim();
    }

    var pageStub = stubText.toLowerCase();

    pageStub = pageStub.replace(/[^a-z0-9- ]/g, '');
    pageStub = pageStub.replace(/^[\s-]+|[\s-]+$/, '');
    pageStub = pageStub.replace(/[\s-]+/g, '-');

    var stub = pageStub;

    if (groupStub)
    {
        stub = groupStub + '/' + stub;
    }

    document.getElementById('preferred-page-stub').value = pageStub;

    var preferredStubView = document.getElementById('preferred-stub-view');

    while (preferredStubView.firstChild)
    {
        preferredStubView.removeChild(preferredStubView.firstChild);
    }

    preferredStubView.appendChild(document.createTextNode(stub));
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

    var stubView = document.getElementById('stub-view');

    while (stubView.firstChild)
    {
        stubView.removeChild(stubView.firstChild);
    }

    var stub = response.pageStub;

    if (groupStub)
    {
        stub = groupStub + '/' + stub;
    }

    stubView.appendChild(document.createTextNode(stub));
}

document.addEventListener('DOMContentLoaded', handleContentLoaded, false);

</script>

<?php

$page->end();

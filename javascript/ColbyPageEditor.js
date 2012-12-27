"use strict";

var ColbyPageEditor =
{
    'formManager' : null
};

/**
 * @return void
 */
function handleContentLoaded()
{
    ColbyPageEditor.formManager = new ColbyFormManager(ajaxURL);

    ColbyPageEditor.formManager.updateCompleteCallback = updateCompleteCallback;

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
        if (!publicationDate)
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

    preferredStubView.textContent = stub;
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

    ColbyPageEditor.formManager.setNeedsUpdate(true);
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

    var stub = response.pageStub;

    if (groupStub)
    {
        stub = groupStub + '/' + stub;
    }

    var stubView = document.getElementById('stub-view');

    stubView.textContent = stub;
}

document.addEventListener('DOMContentLoaded', handleContentLoaded, false);

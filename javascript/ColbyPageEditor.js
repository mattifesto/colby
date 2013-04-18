"use strict";

var ColbyPageEditor =
{
    'formManager'           : null,
    'naturalBaseStub'       : null,
    'naturalURI'            : null
};

/**
 * @return void
 */
ColbyPageEditor.display = function()
{
    /**
     * Elements
     */

    var uriElement = document.getElementById('uri');
    var publicationDateTextElement = document.getElementById('publication-date-text');

    /**
     * State
     */

    var isPublished = document.getElementById('is-published').checked;
    var uriIsCustom = document.getElementById('uri-is-custom').value;
    var publishedTimeStamp = document.getElementById('published-time-stamp').value;

    /**
     * Update
     */

    if (isPublished)
    {
        uriElement.disabled = true;
        publicationDateTextElement.disabled = true;
    }
    else
    {
        uriElement.disabled = false;
        publicationDateTextElement.disabled = false;
    }

    if (!publicationDateTextElement.classList.contains('invalid'))
    {
        if (publishedTimeStamp)
        {
            var date = new Date(publishedTimeStamp * 1000);

            publicationDateTextElement.value = date.toLocaleString();
        }
        else
        {
            publicationDateTextElement.value = '';
        }
    }

    if (!uriIsCustom)
    {
        uriElement.value = ColbyPageEditor.naturalURI;
    }
};

/**
 * @return void
 */
ColbyPageEditor.handleContentLoaded = function()
{
    ColbyPageEditor.formManager = new ColbyFormManager(ajaxURL);

    ColbyPageEditor.formManager.updateCompleteCallback = ColbyPageEditor.updateCompleteCallback;

    document.getElementById('is-published').addEventListener(
        'change', ColbyPageEditor.handleIsPublishedChanged, false);

    document.getElementById('publication-date-text').addEventListener(
        'blur', ColbyPageEditor.handlePublicationDateTextBlurred, false);

    document.getElementById('title').addEventListener(
        'input', ColbyPageEditor.handleTitleReceivedInput, false);

    document.getElementById('uri').addEventListener(
        'input', ColbyPageEditor.handleURIReceivedInput, false);

    document.getElementById('uri').addEventListener(
        'blur', ColbyPageEditor.handleURIBlurred, false);

    /**
     * If this is a new document it won't have a URI value yet so update the
     * natural URI.
     */

    ColbyPageEditor.updateNaturalURI();

    /**
     * Update all of the elements that have dependencies.
     */

    ColbyPageEditor.display();
};

/**
 * @return void
 */
ColbyPageEditor.handlePublicationDateTextBlurred = function(event)
{
    var publicationDateTextElement = event.target;
    var publishedTimeStampElement = document.getElementById('published-time-stamp');

    if (publicationDateTextElement.value.match(/^\s*$/))
    {
        /**
         * The input contains only whitespace so set the time stamp value to
         * `null` (empty string) and the text input value to an empty string.
         */

        publishedTimeStampElement.value = '';
        publicationDateTextElement.value = '';
    }
    else
    {
        var date = new Date(publicationDateTextElement.value);

        if (isNaN(date))
        {
            /**
             * If the date string is invalid, set the time stamp value to
             * `null` (empty string).
             */

            publishedTimeStampElement.value = '';

            publicationDateTextElement.classList.add('invalid');

            return;
        }
        else
        {
            // `Math.floor` guarantees an integer value

            var unixTimeStamp = Math.floor(date.getTime() / 1000);

            publishedTimeStampElement.value = unixTimeStamp;
        }
    }

    publicationDateTextElement.classList.remove('invalid');
    publicationDateTextElement.classList.add('needs-update');

    /**
     * Make sure the change is saved to the server.
     */

    ColbyPageEditor.formManager.setNeedsUpdate(true);

    /**
     * The `display` method will update the publication date text input to
     * a cononical date format.
     */

    ColbyPageEditor.display();
};

/**
 * @return void
 */
ColbyPageEditor.handleIsPublishedChanged = function(event)
{
    var isPublishedElement = event.target;

    if (isPublishedElement.checked)
    {
        /**
         * If the published time stamp isn't set, set it to the current time
         * stamp.
         */

        var publishedTimeStampElement = document.getElementById('published-time-stamp');

        if (!publishedTimeStampElement.value)
        {
            /**
             * `Math.floor` guarantees an integer value.
             */

            var unixTimeStamp = Math.floor(Date.now() / 1000);

            publishedTimeStampElement.value = unixTimeStamp;

            /**
             * Highlight the publication date input so the user will see that
             * it has changed. (The `display` method will update its value.)
             */

            document.getElementById('publication-date-text').classList.add('needs-update');
        }

        /**
         * If `published-by` isn't set, set it to the if of the current user.
         */

        var publishedByElement = document.getElementById('published-by');

        if (!publishedByElement.value)
        {
            // TODO: Get the current user id from HTML, not global variable.

            publishedByElement.value = currentUserId;
        }

        /**
         * When the document is published we specify that the URI is custom so
         * that it will no longer change if the title is changed.
         */

        document.getElementById('uri-is-custom').value = 'true';
    }

    ColbyPageEditor.display();
};

/**
 * @return void
 */
ColbyPageEditor.handleTitleReceivedInput = function(event)
{
    ColbyPageEditor.updateNaturalURI();

    ColbyPageEditor.display();
};

/**
 * This function may make changes to the `uri` element's value. It does not
 * call the `display` method because it doesn't change any dependencies that
 * would result in any other visual changes.
 *
 * @return void
 */
ColbyPageEditor.handleURIBlurred = function()
{
    var uriElement = document.getElementById('uri')

    var uri = Colby.textToURI(uriElement.value);

    if (!uri || uri == ColbyPageEditor.naturalURI)
    {
        uri = ColbyPageEditor.naturalURI;

        document.getElementById('uri-is-custom').value = '';
    }
    else
    {
        document.getElementById('uri-is-custom').value = 'true';
    }

    document.getElementById('uri').value = uri;
};

/**
 * This method does not call the `display` method because at the time it is
 * called the user is providing input and there are no other visual updates
 * required to the page.
 *
 * @return void
 */
ColbyPageEditor.handleURIReceivedInput = function()
{
    document.getElementById('uri-is-custom').value = 'true';
};

/**
 * @return void
 */
ColbyPageEditor.updateNaturalURI = function()
{
    var title = document.getElementById('title').value;

    var uri = Colby.textToURI(title);

    if (!uri)
    {
        uri = document.getElementById('archive-id').value;
    }

    if (ColbyPageEditor.naturalBaseStub)
    {
        uri = ColbyPageEditor.naturalBaseStub + '/' + uri;
    }

    ColbyPageEditor.naturalURI = uri;
};

/**
 * @return void
 */
ColbyPageEditor.updateCompleteCallback = function(response)
{
    if (!response.wasSuccessful)
    {
        return;
    }

    Colby.updateTimestampForElementWithId(Date.now(), 'modified');

    if (response.uriIsAvailable)
    {
        var uriElement = document.getElementById('uri').classList.remove('invalid');
    }
    else
    {
        var uriElement = document.getElementById('uri').classList.add('invalid');
    }

    // Inform any other interested parties that the update completed by using a custom event.

    var event = new CustomEvent('ColbyPageUpdateComplete', { 'detail' : response });

    document.dispatchEvent(event);
};

document.addEventListener('DOMContentLoaded', ColbyPageEditor.handleContentLoaded, false);

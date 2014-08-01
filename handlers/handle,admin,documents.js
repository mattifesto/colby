"use strict";

var ColbyArchivesExplorer = {};

/**
 * @return void
 */
ColbyArchivesExplorer.regenerateDocument = function()
{
    if (ColbyArchivesExplorer.documentIsRegenerating)
    {
        return;
    }

    ColbyArchivesExplorer.partIndex = 0;
    ColbyArchivesExplorer.documentIsRegenerating = true;

    var progressElement = document.getElementById('progress');

    progressElement.value = 0;

    ColbyArchivesExplorer.regeneratePart();
};

/**
 * @return void
 */
ColbyArchivesExplorer.regeneratePart = function()
{
    var xhr = new XMLHttpRequest();

    var handleAjaxResponse = function()
    {
        ColbyArchivesExplorer.regeneratePartCompleted(xhr);
    };

    var formData = new FormData();
    formData.append("part-index", ColbyArchivesExplorer.partIndex);

    xhr.open('POST', '/admin/documents/ajax/explore-archives/', true);
    xhr.onload = handleAjaxResponse;
    xhr.send(formData);
};

/**
 * @return void
 */
ColbyArchivesExplorer.regeneratePartCompleted = function(xhr)
{
    ColbyArchivesExplorer.partIndex++;

    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);

        ColbyArchivesExplorer.documentIsRegenerating = false;

        return;
    }

    var progressElement = document.getElementById('progress');

    progressElement.value = ColbyArchivesExplorer.partIndex;

    if (ColbyArchivesExplorer.partIndex < 256)
    {
        ColbyArchivesExplorer.regeneratePart();
    }
    else
    {
        progressElement.value = 0;
        ColbyArchivesExplorer.documentIsRegenerating = false;
    }
};

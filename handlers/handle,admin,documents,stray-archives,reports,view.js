"use strict";

var ColbyReportArchiveDeleter = {};

/**
 * @return void
 */
ColbyReportArchiveDeleter.deleteArchives = function()
{
    if (ColbyReportArchiveDeleter.isRunning)
    {
        return;
    }

    ColbyReportArchiveDeleter.reportId = document.getElementById("report-id").value;
    ColbyReportArchiveDeleter.isRunning = true;

    var progressElement = document.getElementById('progress');

    progressElement.removeAttribute('value');

    ColbyReportArchiveDeleter.sendAjaxRequest();
};

/**
 * @return void
 */
ColbyReportArchiveDeleter.sendAjaxRequest = function()
{
    var xhr = new XMLHttpRequest();

    var formData = new FormData();
    formData.append("report-id", ColbyReportArchiveDeleter.reportId);

    xhr.open('POST', '/admin/documents/stray-archives/reports/ajax/delete-report-archives/', true);
    xhr.onload = function() { ColbyReportArchiveDeleter.ajaxRequestCompleted(xhr) };
    xhr.send(formData);
};

/**
 * @return void
 */
ColbyReportArchiveDeleter.ajaxRequestCompleted = function(xhr)
{
    ColbyReportArchiveDeleter.partIndex++;

    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);

        ColbyReportArchiveDeleter.isRunning = false;

        return;
    }

    if (response.hasMoreArchivesToDelete)
    {
        ColbyReportArchiveDeleter.sendAjaxRequest();
    }
    else
    {
        var progressElement                 = document.getElementById('progress');
        progressElement.value               = 0;
        ColbyReportArchiveDeleter.isRunning = false;

        Colby.displayResponse(response);
    }
};

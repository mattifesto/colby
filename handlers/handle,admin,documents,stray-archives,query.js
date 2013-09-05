"use strict";

var ColbyStrayArchivesFinder = {};

ColbyStrayArchivesFinder.runQuery = function()
{
    if (ColbyStrayArchivesFinder.queryIsRunning)
    {
        return;
    }

    var reportName = document.getElementById("report-name").value.trim();

    if (!reportName)
    {
        ColbySheet.alert('You must enter a name for the report.');

        return;
    }

    var queryFieldName = document.getElementById("query-field-name").value.trim();

    if (!queryFieldName)
    {
        ColbySheet.alert('You must enter a field name to query.');

        return;
    }

    var queryFieldValue = document.getElementById("query-field-value").value;

    ColbyStrayArchivesFinder.reportName = reportName;
    ColbyStrayArchivesFinder.queryFieldName = queryFieldName;
    ColbyStrayArchivesFinder.queryFieldValue = queryFieldValue;
    ColbyStrayArchivesFinder.partIndex = 0;
    ColbyStrayArchivesFinder.queryIsRunning = true;

    var progressElement = document.getElementById('progress');

    progressElement.value = 0;

    ColbyStrayArchivesFinder.runQueryForPart();
};

ColbyStrayArchivesFinder.runQueryForPart = function()
{
    var xhr = new XMLHttpRequest();

    var handleAjaxResponse = function()
    {
        ColbyStrayArchivesFinder.runQueryForPartCompleted(xhr);
    };

    var formData = new FormData();
    formData.append("part-index", ColbyStrayArchivesFinder.partIndex);
    formData.append("report-name", ColbyStrayArchivesFinder.reportName);
    formData.append("query-field-name", ColbyStrayArchivesFinder.queryFieldName);
    formData.append("query-field-value", ColbyStrayArchivesFinder.queryFieldValue);

    xhr.open('POST', '/admin/documents/stray-archives/query/ajax/run-query/', true);
    xhr.onload = handleAjaxResponse;
    xhr.send(formData);
};

ColbyStrayArchivesFinder.runQueryForPartCompleted = function(xhr)
{
    ColbyStrayArchivesFinder.partIndex++;

    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);

        ColbyStrayArchivesFinder.queryIsRunning = false;

        return;
    }

    var progressElement = document.getElementById('progress');

    progressElement.value = ColbyStrayArchivesFinder.partIndex;

    if (ColbyStrayArchivesFinder.partIndex < 256)
    {
        ColbyStrayArchivesFinder.runQueryForPart();
    }
    else
    {
        ColbyStrayArchivesFinder.queryIsRunning = false;
    }
};

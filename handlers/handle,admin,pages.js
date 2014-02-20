"use strict";

var CBPagesAdmin = {};

/**
 * @return void
 */
CBPagesAdmin.deletePageWithDataStoreID = function(dataStoreID)
{
    var formData = new FormData();
    formData.append("dataStoreID", dataStoreID);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/pages/api/delete/", true);
    xhr.onload = CBPagesAdmin.deletePageWithDataStoreIDDidComplete;
    xhr.send(formData);
}

/**
 * @return void
 */
CBPagesAdmin.deletePageWithDataStoreIDDidComplete = function()
{
    var xhr         = this;
    var response    = Colby.responseFromXMLHttpRequest(xhr);

    if (response.wasSuccessful)
    {
        var trElementID     = "id-" + response.dataStoreID;
        var trElement       = document.getElementById(trElementID);
        trElement.parentElement.removeChild(trElement);
    }
    else
    {
        Colby.displayResponse(response);
    }
}

/**
 * @return void
 */
CBPagesAdmin.searchForPages = function()
{
    var queryTextElement = document.getElementById("queryText");
    queryTextElement.disabled = true;

    var formData = new FormData();
    formData.append("queryText", queryTextElement.value);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/pages/api/search/", true);
    xhr.onload = CBPagesAdmin.searchForPagesDidComplete;
    xhr.send(formData);
}

/**
 * @return void
 */
CBPagesAdmin.searchForPagesDidComplete = function()
{
    var xhr         = this;
    var response    = Colby.responseFromXMLHttpRequest(xhr);

    if (response.wasSuccessful)
    {
        CBPagesAdmin.showSearchResults(response.matches);
    }
    else
    {
        Colby.displayResponse(response);
    }

    var queryTextElement = document.getElementById("queryText");
    queryTextElement.disabled = false;
}

/**
 * @return void
 */
CBPagesAdmin.showSearchResults = function(matches)
{
    var queryResults = document.getElementById("queryResults");

    while (queryResults.firstChild)
    {
        queryResults.removeChild(queryResults.firstChild);
    }

    var countOfMatches = matches.length;

    for (var i = 0; i < countOfMatches; i++)
    {
        var tr = CBSearchResultsRow.make(matches[i]);

        queryResults.appendChild(tr);
    }

    Colby.updateTimes();
}


/**
 * This object will eventually cache table rows to be re-used.
 */
var CBSearchResultsRow = {};

/**
 * @return `tr` element
 */
CBSearchResultsRow.make = function(match)
{
    var tr  = document.createElement("tr");
    tr.id   = "id-" + match.dataStoreID;


    var actionsCell = document.createElement("td");
    actionsCell.classList.add("actions-cell");

    var editLink    = document.createElement("a");
    editLink.href   = "/admin/pages/edit/?data-store-id=" + match.dataStoreID;
    editLink.classList.add("action");
    editLink.appendChild(document.createTextNode("edit"));
    actionsCell.appendChild(editLink);

    var deleteLink  = document.createElement("a");
    deleteLink.classList.add("action");
    deleteLink.addEventListener('click', CBSearchResultsRow.deleteHandlerForDataStoreID(match.dataStoreID), false);
    deleteLink.appendChild(document.createTextNode("delete"));
    actionsCell.appendChild(deleteLink);

    tr.appendChild(actionsCell);


    var titleCell       = document.createElement("td");
    titleCell.innerHTML = match.titleHTML;
    titleCell.classList.add("title-cell");

    tr.appendChild(titleCell);


    var publicationDateCell = document.createElement("td");
    publicationDateCell.classList.add("publication-date-cell");

    var dateSpan = document.createElement("span");
    dateSpan.classList.add("time");
    dateSpan.setAttribute("data-timestamp", match.published ? match.published * 1000 : "");
    publicationDateCell.appendChild(dateSpan);

    tr.appendChild(publicationDateCell);


    return tr;
}

/**
 * @return function
 */
CBSearchResultsRow.deleteHandlerForDataStoreID = function(dataStoreID)
{
    var handler = function()
    {
        CBPagesAdmin.deletePageWithDataStoreID(dataStoreID);
    };

    return handler;
};

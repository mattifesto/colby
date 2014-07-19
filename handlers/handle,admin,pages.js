"use strict";

var CBPagesAdmin = {};


/**
 * @return void
 */
CBPagesAdmin.movePageWithDataStoreIDToTrash = function(dataStoreID)
{
    var formData = new FormData();
    formData.append("dataStoreID", dataStoreID);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/pages/api/move-to-the-trash/", true);
    xhr.onload = CBPagesAdmin.movePageWithDataStoreIDToTrashDidComplete;
    xhr.send(formData);
};

/**
 * @return void
 */
CBPagesAdmin.movePageWithDataStoreIDToTrashDidComplete = function()
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
};

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
};

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
};

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
};


/**
 * This object will eventually cache table rows to be re-used.
 */
var CBSearchResultsRow = {};

/**
 * @return function
 */
CBSearchResultsRow.editPageWithDataStoreID = function(dataStoreID)
{
    location.href = "/admin/pages/edit/?data-store-id=" + dataStoreID;;
};

/**
 * @return `tr` element
 */
CBSearchResultsRow.make = function(match)
{
    var tr  = document.createElement("tr");
    tr.id   = "id-" + match.dataStoreID;


    var actionsCell = document.createElement("td");
    actionsCell.classList.add("actions-cell");

    var editButton          = document.createElement("button");
    var editHandler         = CBSearchResultsRow.editPageWithDataStoreID.bind(null, match.dataStoreID);
    editButton.textContent  = "Edit";
    editButton.addEventListener("click", editHandler, false);
    actionsCell.appendChild(editButton);

    var moveToTrashButton           = document.createElement("button");
    var moveToTrashHandler          = CBSearchResultsRow.moveToTrashHandlerForDataStoreID(match.dataStoreID);
    moveToTrashButton.textContent   = "Move to Trash";
    moveToTrashButton.addEventListener("click", moveToTrashHandler, false);
    actionsCell.appendChild(moveToTrashButton);

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
};

/**
 * @return function
 */
CBSearchResultsRow.moveToTrashHandlerForDataStoreID = function(dataStoreID)
{
    var handler = function()
    {
        CBPagesAdmin.movePageWithDataStoreIDToTrash(dataStoreID);
    };

    return handler;
};

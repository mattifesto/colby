"use strict";

var CBPagesSearchFormView = {};

/**
 * @return void
 */
CBPagesSearchFormView.init = function()
{
    var searchFormContainer         = document.getElementById("CBPagesSearchFormView");
    this.searchTextField            = document.createElement("input");
    this.searchTextField.type       = "text";
    this.searchButton               = document.createElement("button");
    this.searchButton.textContent   = "Search";

    this.searchTextField.addEventListener("keyup", this.searchTextFieldKeyUp.bind(this));
    this.searchButton.addEventListener("click", this.searchForPages.bind(this));

    searchFormContainer.appendChild(this.searchTextField);
    searchFormContainer.appendChild(this.searchButton);

    this.searchTextField.focus();

    return this;
};

/**
 * @return void
 */
CBPagesSearchFormView.searchForPages = function()
{
    this.searchButton.disabled      = true;
    this.searchTextField.disabled   = true;

    var formData = new FormData();
    formData.append("queryText", this.searchTextField.value);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/pages/api/search/", true);
    xhr.onload = this.searchForPagesDidComplete.bind(this);
    xhr.send(formData);

    this.searchXHR = xhr;
};

/**
 * @return void
 */
CBPagesSearchFormView.searchForPagesDidComplete = function()
{
    this.searchButton.disabled      = false;
    this.searchTextField.disabled   = false;

    var response    = Colby.responseFromXMLHttpRequest(this.searchXHR);

    if (response.wasSuccessful)
    {
        this.showSearchResults(response.matches);
    }
    else
    {
        Colby.displayResponse(response);
    }
};

/**
 * @return void
 */
CBPagesSearchFormView.searchTextFieldKeyUp = function(event)
{
    if (13 == event.keyCode)
    {
        this.searchForPages();
    }
};

/**
 * @return void
 */
CBPagesSearchFormView.showSearchResults = function(matches)
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
    var moveToTrashHandler          = CBPagesAdmin.movePageWithDataStoreIDToTrash.bind(null,
                                                                                       match.dataStoreID);
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
 * 2014.07.19
 * The CBPagesSearchFormView was created with the intent of creating an
 * instance of the class in response to `DOMContentLoaded`:
 *
 *      Object.create(CBPagesSearchFormView).init();
 *
 * Then I realized that the object itself is an instance. Since it is
 * basically a singleton, just calling `init` on the instance created by
 * defining it is sufficient.
 *
 * Ironically, that makes this very modern code mimic some older code so I'm
 * adding this comment to explain since this is the first time I've used this
 * pattern.
 */

document.addEventListener("DOMContentLoaded", function() { CBPagesSearchFormView.init(); });

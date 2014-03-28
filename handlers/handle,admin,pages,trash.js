"use strict";

var CBPagesInTheTrash = {};

/**
 * @return void
 */
CBPagesInTheTrash.deletePageWithDataStoreID = function(dataStoreID)
{
    var formData = new FormData();
    formData.append("dataStoreID", dataStoreID);

    var xhr     = new XMLHttpRequest();
    xhr.onload  = CBPagesInTheTrash.deletePageWithDataStoreIDDidComplete;
    xhr.open("POST", "/admin/pages/api/permanently-delete-from-the-trash/", true);
    xhr.send(formData);
};

/**
 * @return void
 */
CBPagesInTheTrash.deletePageWithDataStoreIDDidComplete = function()
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
CBPagesInTheTrash.recoverPageWithDataStoreID = function(dataStoreID)
{
    var formData = new FormData();
    formData.append("dataStoreID", dataStoreID);

    var xhr     = new XMLHttpRequest();
    xhr.onload  = CBPagesInTheTrash.recoverPageWithDataStoreIDDidComplete;
    xhr.open("POST", "/admin/pages/api/recover-from-the-trash/", true);
    xhr.send(formData);
};

/**
 * @return void
 */
CBPagesInTheTrash.recoverPageWithDataStoreIDDidComplete = function()
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

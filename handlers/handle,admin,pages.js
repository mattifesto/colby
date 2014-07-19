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

"use strict";

var CBPagesAdmin = {};

/**
 * @return void
 */
CBPagesAdmin.deletePageByDataStoreID = function(dataStoreID)
{
    var formData = new FormData();
    formData.append("dataStoreID", dataStoreID);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/pages/api/delete/", true);
    xhr.onload = CBPagesAdmin.deletePageByDataStoreIDDidComplete;
    xhr.send(formData);
}

/**
 * @return void
 */
CBPagesAdmin.deletePageByDataStoreIDDidComplete = function()
{
    var xhr         = this;
    var response    = Colby.responseFromXMLHttpRequest(xhr);

    if (response.wasSuccessful)
    {
        var trElementID     = "s" + response.dataStoreID;
        var trElement       = document.getElementById(trElementID);
        trElement.parentElement.removeChild(trElement);
    }
    else
    {
        Colby.displayResponse(response);
    }
}

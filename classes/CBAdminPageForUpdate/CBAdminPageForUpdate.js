"use strict";

var ColbySiteUpdater = {};

ColbySiteUpdater.update = function(sender)
{
    sender.disabled = true;

    var xhr = new XMLHttpRequest();

    var handleAjaxResponse = function()
    {
        var response = Colby.responseFromXMLHttpRequest(xhr);

        Colby.displayResponse(response);

        document.getElementById('progress').setAttribute('value', 0);

        sender.disabled = false;
    };

    xhr.open('POST', '/api/?class=CBAdminPageForUpdate&function=updateForAjax', true);
    xhr.onload = handleAjaxResponse;
    xhr.send();

    document.getElementById('progress').removeAttribute('value');
};

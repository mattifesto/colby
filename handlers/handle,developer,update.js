"using strict";

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

    xhr.open('POST', '/developer/update/ajax/perform-update/', true);
    xhr.onload = handleAjaxResponse;
    xhr.send();

    document.getElementById('progress').removeAttribute('value');
}

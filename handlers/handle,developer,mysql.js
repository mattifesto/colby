"using strict";

var DeveloperMySQL = {};

DeveloperMySQL.backupDatabase = function(sender)
{
    sender.disabled = true;

    var xhr = new XMLHttpRequest();

    var handleAjaxResponse = function()
    {
        var response = Colby.responseFromXMLHttpRequest(xhr);

            Colby.displayResponse(response);

        document.getElementById('backup-database-progress').setAttribute('value', 0);

        sender.disabled = false;
    };

    xhr.open('POST', '/developer/mysql/ajax/backup-database/', true);
    xhr.onload = handleAjaxResponse;
    xhr.send();

    document.getElementById('backup-database-progress').removeAttribute('value');
}

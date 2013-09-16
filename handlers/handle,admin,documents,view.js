"use strict";

var ColbyDocumentDeleter = {};

/**
 *
 */
ColbyDocumentDeleter.deleteDocument = function()
{
    var archiveId = document.getElementById("archive-id").value;
    var archiveIdForConfirmation = document.getElementById("archive-id-for-confirmation").value;

    if (archiveId != archiveIdForConfirmation)
    {
        ColbySheet.alert("You must enter the document's archive id to enable deletion.");

        return;
    }

    var formData = new FormData();

    formData.append("archive-id", archiveId);

    var xhr = new XMLHttpRequest();

    xhr.open("POST", "/admin/documents/view/ajax/delete-document/", true);
    xhr.onload = function() { ColbyDocumentDeleter.requestDeleteForDocumentWithArchiveIdCompleted(xhr); };
    xhr.send(formData);
};

/**
 *
 */
ColbyDocumentDeleter.requestDeleteForDocumentWithArchiveIdCompleted = function(xhr)
{
    var response = Colby.responseFromXMLHttpRequest(xhr);

    Colby.displayResponse(response);
};

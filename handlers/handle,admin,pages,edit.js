"use strict";

/**
 *
 */
var CBPageEditor = {};

/**
 * @return Object
 */
CBPageEditor.createModel = function()
{
    var model = {};

    model.schema =          "CBPage";
    model.schemaVersion =   1;
    model.archiveID =       CBURLQueryVariables["archive-id"];
    model.groupID =         null;
    model.title =           "";
    model.titleHTML =       "";
    model.subtitle =        "";
    model.subtitleHTML =    "";
    model.URI =             null;
    model.published =       null;
    model.publishedBy =     null;
    model.thumbnailURL =    null;
    model.headerSection =   null;
    model.bodySections =    [];
    model.footerSection =   null;

    return model;
}

/**
 * @return void
 */
CBPageEditor.loadModel = function()
{
    var formData = new FormData();

    formData.append("archive-id", CBURLQueryVariables["archive-id"]);

    var xhr = new XMLHttpRequest();

    xhr.open("POST", "/admin/pages/api/get-model/", true);
    xhr.onload = CBPageEditor.loadModelDidComplete;
    xhr.send(formData);
};

/**
 * @return void
 */
CBPageEditor.loadModelDidComplete = function()
{
    var response = Colby.responseFromXMLHttpRequest(this);

    if (response.wasSuccessful)
    {
        if ("model" in response)
        {
            CBPageEditor.model = response.model;

            ColbySheet.alert(JSON.stringify(response.model));
        }
        else
        {
            CBPageEditor.model = CBPageEditor.createModel();
        }
    }
    else
    {
        Colby.displayResponse(response);
    }
};

/**
 * @return void
 */
CBPageEditor.DOMContentDidLoad = function()
{
    CBPageEditor.loadModel();
};

/**
 *
 */
document.addEventListener('DOMContentLoaded', CBPageEditor.DOMContentDidLoad, false);

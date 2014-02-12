"use strict";

/**
 *
 */
var CBPageEditor =
{
    sectionEditors: {},
    model:          null,
    needsCreating:  false,
    needsSaving:    false,
};

/**
 * @return void
 */
CBPageEditor.appendPageTemplateOption = function(template)
{
    var textNode            = document.createTextNode(template.name);
    var pageTemplateOption  = document.createElement("div");
    pageTemplateOption.classList.add("page-template-option");
    pageTemplateOption.appendChild(textNode);

    var handler = function()
    {
        CBPageEditor.model              = JSON.parse(template.modelJSON);
        CBPageEditor.model.dataStoreID  = CBURLQueryVariables["data-store-id"];
        CBPageEditor.needsCreating      = true;

        CBPageEditor.displayEditor();
    }

    pageTemplateOption.addEventListener("click", handler, false);

    var mainElement = document.getElementsByTagName("main")[0];
    mainElement.appendChild(pageTemplateOption);
};

/**
 * @return void
 */
CBPageEditor.displayEditor = function()
{
    var mainElement = document.getElementsByTagName("main")[0];

    while (mainElement.firstChild)
    {
        mainElement.removeChild(mainElement.firstChild);
    }

    /**
     * Menu
     */

    var nav         = document.createElement("nav");
    var preview     = document.createElement("a");
    preview.href    = "/admin/document/preview/?archive-id=" + CBURLQueryVariables["data-store-id"];
    preview.appendChild(document.createTextNode("preview"));
    nav.appendChild(preview);
    mainElement.appendChild(nav);

    /**
     * Page information
     */

    var section = new CBSection("Page information");

    mainElement.appendChild(section.outerElement());

    var page = new CBPageInformation(CBPageEditor.model, null, section.innerElement());


    /**
     *
     */

    var displaySection = function(sectionModel)
    {
        var sectionTitle    = CBSectionDescriptors[sectionModel.sectionTypeID].name;
        var section         = new CBSection(sectionTitle);

        mainElement.appendChild(section.outerElement());

        if (sectionModel)
        {
            var sectionEditorConstructor = CBPageEditor.sectionEditors[sectionModel.sectionTypeID];

            var section = new sectionEditorConstructor(CBPageEditor.model, sectionModel, section.innerElement());
        }
        else
        {
            sectionElement.appendChild(document.createTextNode('No section specified'));
        }
    };


    /**
     * Sections
     */

    CBPageEditor.model.sectionModels.forEach(displaySection);
};

/**
 * @return void
 */
CBPageEditor.displayPageTemplateChooser = function()
{
    var mainElement = document.getElementsByTagName("main")[0];

    while (mainElement.firstChild)
    {
        mainElement.removeChild(mainElement.firstChild);
    }

    for (var ID in CBPageTemplateDescriptors)
    {
        CBPageEditor.appendPageTemplateOption(CBPageTemplateDescriptors[ID]);
    }
};

/**
 * @return void
 */
CBPageEditor.DOMContentDidLoad = function()
{
    document.dispatchEvent(new Event("CBPageEditorDidLoad"));

    CBPageEditor.loadModel();
};

/**
 * @return void
 */
CBPageEditor.loadModel = function()
{
    var formData = new FormData();
    formData.append("data-store-id", CBURLQueryVariables["data-store-id"]);

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
        if ("modelJSON" in response)
        {
            CBPageEditor.model = JSON.parse(response.modelJSON);

            CBPageEditor.displayEditor();
        }
        else
        {
            CBPageEditor.displayPageTemplateChooser();
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
CBPageEditor.registerSectionEditor = function(sectionTypeID, sectionEditor)
{
    CBPageEditor.sectionEditors[sectionTypeID] = sectionEditor;
};


/**
 * @return void
 */
CBPageEditor.requestCreate = function()
{
    if (CBPageEditor.requestToCreate)
    {
        return;
    }

    var formData = new FormData();
    formData.append("data-store-id", CBPageEditor.model.dataStoreID);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/pages/api/create/", true);
    xhr.onload = CBPageEditor.requestCreateDidComplete;
    xhr.send(formData);

    CBPageEditor.requestToCreate = xhr;
};

/**
 * @return void
 */
CBPageEditor.requestCreateDidComplete = function()
{
    var response = Colby.responseFromXMLHttpRequest(this);

    if (response.wasSuccessful)
    {
        CBPageEditor.model.rowID        = response.rowID;
        CBPageEditor.requestToCreate    = null;

        CBPageEditor.requestSave();
    }
    else
    {
        Colby.displayResponse(response);
    }
};

/**
 * @return void
 */
CBPageEditor.requestSave = function()
{
    if (!CBPageEditor.model.rowID)
    {
        CBPageEditor.requestCreate();

        return;
    }

    if (!CBPageEditor.continuousAjaxRequestToSave)
    {
        var URI = "/admin/pages/api/save-model/";

        CBPageEditor.continuousAjaxRequestToSave        = new CBContinuousAjaxRequest(URI);
        CBPageEditor.continuousAjaxRequestToSave.delay  = 2000;
        CBPageEditor.continuousAjaxRequestToSave.onload = CBPageEditor.requestSaveDidComplete;
    }

    var formData = new FormData();
    formData.append("model-json", JSON.stringify(CBPageEditor.model));

    CBPageEditor.continuousAjaxRequestToSave.makeRequestWithFormData(formData);
};

/**
 * @return void
 */
CBPageEditor.requestSaveDidComplete = function()
{
    var response = Colby.responseFromXMLHttpRequest(this);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
};

/**
 *
 */
document.addEventListener("DOMContentLoaded", CBPageEditor.DOMContentDidLoad, false);

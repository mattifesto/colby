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

    var editorContainer = document.createElement("div");
    editorContainer.classList.add("CBPageEditor");
    mainElement.appendChild(editorContainer);

    /**
     * Menu
     */

    var nav             = document.createElement("nav");
    var preview         = document.createElement("a");
    var makeFrontPage   = document.createElement("button");

    preview.href = "/admin/document/preview/?archive-id=" + CBURLQueryVariables["data-store-id"];
    makeFrontPage.style.marginLeft = "20px";
    preview.classList.add("standard-link-button");

    preview.appendChild(document.createTextNode("preview"));
    makeFrontPage.appendChild(document.createTextNode("Make This Page the Front Page"));
    makeFrontPage.addEventListener('click', CBPageEditor.makeFrontPage, false);

    nav.appendChild(preview);
    nav.appendChild(makeFrontPage);
    editorContainer.appendChild(nav);

    /**
     * Page information
     */

    var pageInformationEditorView = new CBPageInformationEditorView(CBPageEditor.model);
    editorContainer.appendChild(pageInformationEditorView.element());

    /**
     *
     */

    var sectionListView = new CBSectionListView(CBPageEditor.model.sections);
    editorContainer.appendChild(sectionListView.element());
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
CBPageEditor.makeFrontPage = function()
{
    var formData = new FormData();
    formData.append("dataStoreID", CBURLQueryVariables["data-store-id"]);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/pages/api/make-front-page/", true);
    xhr.onload = CBPageEditor.makeFrontPageDidComplete;
    xhr.send(formData);
};

/**
 * @return void
 */
CBPageEditor.makeFrontPageDidComplete = function()
{
    var xhr         = this;
    var response    = Colby.responseFromXMLHttpRequest(xhr);

    Colby.displayResponse(response);
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
CBPageEditor.requestCreatePageRow = function()
{
    if (CBPageEditor.requestToCreate)
    {
        return;
    }

    var formData = new FormData();
    formData.append("data-store-id", CBPageEditor.model.dataStoreID);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/pages/api/create/", true);
    xhr.onload = CBPageEditor.requestCreatePageRowDidComplete;
    xhr.send(formData);

    CBPageEditor.requestToCreate = xhr;
};

/**
 * @return void
 */
CBPageEditor.requestCreatePageRowDidComplete = function()
{
    var response = Colby.responseFromXMLHttpRequest(this);

    if (response.wasSuccessful)
    {
        CBPageEditor.model.rowID            = response.rowID;
        CBPageEditor.requestToCreatePageRow = null;

        CBPageEditor.requestSave();

        document.dispatchEvent(new Event("CBPageRowWasCreated"));
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
        CBPageEditor.requestCreatePageRow();

        document.addEventListener("CBPageRowWasCreated", CBPageEditor.requestSave, false);

        return;
    }

    if (!CBPageEditor.saveModelAjaxRequest)
    {
        var URI = "/admin/pages/api/save-model/";

        CBPageEditor.saveModelAjaxRequest           = new CBContinuousAjaxRequest(URI);
        CBPageEditor.saveModelAjaxRequest.delay     = 2000;
        CBPageEditor.saveModelAjaxRequest.onload    = CBPageEditor.saveModelAjaxRequestDidComplete;
    }

    var formData = new FormData();
    formData.append("model-json", JSON.stringify(CBPageEditor.model));

    CBPageEditor.saveModelAjaxRequest.makeRequestWithFormData(formData);
};

/**
 * @return void
 */
CBPageEditor.saveModelAjaxRequestDidComplete = function(xhr)
{
    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
};

/**
 *
 */
document.addEventListener("DOMContentLoaded", CBPageEditor.DOMContentDidLoad, false);

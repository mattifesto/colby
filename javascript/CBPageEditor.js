"use strict";

/**
 *
 */
var CBPageEditor =
{
    sectionEditors: {},
    model:          null,
    needsCreating:  false,
    needsSaving:    false
};

/**
 * @return void
 */
CBPageEditor.appendPageTemplateOption = function(template)
{
    if (!CBPageEditor.pageTemplatesSection)
    {
        var mainElement             = document.getElementsByTagName("main")[0];
        var pageTemplatesSection    = document.createElement("section");
        pageTemplatesSection.classList.add("CBPageTemplates");
        mainElement.appendChild(pageTemplatesSection);

        CBPageEditor.pageTemplatesSection = pageTemplatesSection;
    }

    var pageTemplateOption              = document.createElement("div");
    var pageTemplateOptionCell          = document.createElement("div");
    pageTemplateOptionCell.textContent  = template.title;
    pageTemplateOption.classList.add("CBPageTemplateOption");
    pageTemplateOption.appendChild(pageTemplateOptionCell);
    CBPageEditor.pageTemplatesSection.appendChild(pageTemplateOption);


    var handler = function()
    {
        /**
         * The template model will have a unique data store ID but it is
         * replaced here because the editor page has been assigned a data store
         * ID so that a reload will reload the same page instance. There may
         * be opportunity to improve clarity of this process.
         */

        CBPageEditor.model              = JSON.parse(template.modelJSON);
        CBPageEditor.model.dataStoreID  = CBURLQueryVariables["data-store-id"];
        CBPageEditor.needsCreating      = true;

        CBPageEditor.displayEditor();
    };

    pageTemplateOption.addEventListener("click", handler, false);
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

    var element = createPageInformationEditorElement({model: CBPageEditor.model});
    editorContainer.appendChild(element);

    /**
     *
     */

    var sectionListView = CBModelArrayEditor.editorForModelArray(CBPageEditor.model.sections);
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
    this.saveModelTimer                     = Object.create(CBDelayTimer).init();
    this.saveModelTimer.callback            = this.saveModel.bind(this);
    this.saveModelTimer.delayInMilliseconds = 2000;

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
    if (this.pageRowWasRequested)
    {
        return;
    }

    this.pageRowWasRequested = true;

    /**
     * Don't try to save the model until our request is complete.
     */

    this.saveModelTimer.pause();

    var formData = new FormData();
    formData.append("data-store-id", CBPageEditor.model.dataStoreID);

    var xhr     = new XMLHttpRequest();
    xhr.onload  = this.requestCreatePageRowDidComplete.bind(this, xhr);

    xhr.open("POST", "/admin/pages/api/create/", true);
    xhr.send(formData);
};

/**
 * @return void
 */
CBPageEditor.requestCreatePageRowDidComplete = function(xhr)
{
    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (response.wasSuccessful)
    {
        CBPageEditor.model.rowID = response.rowID;

        document.dispatchEvent(new Event("CBPageRowWasCreated"));

        /**
         * Now that the row has been created, model saves can resume.
         */

        this.saveModelTimer.resume();
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
    this.saveModelTimer.restart();

    if (!this.model.rowID && !this.pageRowWasRequested)
    {
        this.requestCreatePageRow();
    }
};

/**
 * Do not call this function directly. It should only be called by the save
 * model timer.
 *
 * @return void
 */
CBPageEditor.saveModel = function() {

    var timeStampInSeconds  = Math.floor(Date.now() / 1000);
    this.model.updated      = timeStampInSeconds;

    if (!this.model.created) {

        this.model.created = timeStampInSeconds;
    }

    var formData = new FormData();
    formData.append("model-json", JSON.stringify(this.model));

    var xhr     = new XMLHttpRequest();
    xhr.onload  = this.saveModelAjaxRequestDidComplete.bind(this, xhr);

    xhr.open("POST", "/admin/pages/api/save-model/");
    xhr.send(formData);

    /**
     * Prevent another callback while the model is being saved.
     */

    this.saveModelTimer.pause();
};

/**
 * @return void
 */
CBPageEditor.saveModelAjaxRequestDidComplete = function(xhr)
{
    /**
     * Resume callbacks.
     */

    this.saveModelTimer.resume();

    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
};


(function()
{
    var listener = CBPageEditor.DOMContentDidLoad.bind(CBPageEditor);

    document.addEventListener("DOMContentLoaded", listener, false);
})();

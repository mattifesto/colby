"use strict";

var CBPageEditor = {
    model               : null,

    /**
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleTitleChanged  : function(args) {
        var title       = args.spec.title || "";
        title           = title.trim();
        title           = (title.length > 0) ? ": " + title : "";
        document.title  = "Page Editor" + title;
    }
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

    preview.href = "/admin/pages/preview/?ID=" + CBURLQueryVariables["data-store-id"];
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

    var element = CBPageInformationEditorFactory.createEditor({
        handleSpecChanged       : function() { CBPageEditor.requestSave(); },
        handleTitleChanged      : CBPageEditor.handleTitleChanged.bind(undefined, {
            spec                : CBPageEditor.model
        }),
        spec                    : CBPageEditor.model });

    editorContainer.appendChild(element);

    /**
     *
     */

    editorContainer.appendChild(CBSpecArrayEditorFactory.createEditor({
        array           : CBPageEditor.model.sections,
        classNames      : CBPageEditorAvailableViewClassNames,
        handleChanged   : CBPageEditor.requestSave.bind(CBPageEditor)
    }));

    CBPageEditor.handleTitleChanged({spec : CBPageEditor.model});
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

    CBPageEditor.fetchModel();
};

/**
 * @return void
 */
CBPageEditor.fetchModel = function() {
    var formData = new FormData();
    formData.append("id", CBURLQueryVariables["data-store-id"]);

    if (CBURLQueryVariables["id-to-copy"] !== undefined) {
        formData.append("id-to-copy", CBURLQueryVariables["id-to-copy"]);
    }

    var xhr = new XMLHttpRequest();
    xhr.onload = CBPageEditor.fetchModelDidLoad.bind(undefined, {
        xhr : xhr
    });
    xhr.open("POST", "/api/?class=CBViewPage&function=fetchSpec");
    xhr.send(formData);
};

/**
 * @return undefined
 */
CBPageEditor.fetchModelDidLoad = function(args) {
    var response = Colby.responseFromXMLHttpRequest(args.xhr);

    if (response.wasSuccessful) {
        if ("modelJSON" in response) {
            CBPageEditor.model = JSON.parse(response.modelJSON);

            if (CBPageEditor.model.sections === undefined) {
                CBPageEditor.model.sections = [];
            }

            CBPageEditor.displayEditor();
        } else {
            CBPageEditor.displayPageTemplateChooser();
        }
    } else {
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
CBPageEditor.requestSave = function()
{
    this.saveModelTimer.restart();
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
    xhr.onerror = this.saveModelAjaxRequestDidFail.bind(this, xhr);

    xhr.open("POST", "/api/?class=CBViewPage&function=saveEditedPage");
    xhr.send(formData);

    /**
     * Prevent another callback while the model is being saved.
     */

    this.saveModelTimer.pause();
};

/**
 * @return undefined
 */
CBPageEditor.saveModelAjaxRequestDidComplete = function(xhr) {
    this.saveModelTimer.resume();

    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (response.wasSuccessful) {
        if ('iteration' in this.model) {
            this.model.iteration++;
        } else {
            this.model.iteration    = 1;
        }
    } else {
        Colby.displayResponse(response);
    }
};

/**
 * Sometimes the server will reject a request. Without this handler, that
 * rejection will silently stop all future attempts to save even though they
 * would most likely complete successfully. This handler could do more, but
 * what it does do is allow future save requests to go through.
 *
 * It also restarts the timer since there is still data to be saved.
 *
 * TODO: Keep a fail count or something an eventually report to the user that
 * communication with the server is not working.
 *
 * @return undefined
 */
CBPageEditor.saveModelAjaxRequestDidFail = function(xhr) {
    this.saveModelTimer.resume();
    this.saveModelTimer.restart();
};

(function()
{
    var listener = CBPageEditor.DOMContentDidLoad.bind(CBPageEditor);

    document.addEventListener("DOMContentLoaded", listener, false);
})();

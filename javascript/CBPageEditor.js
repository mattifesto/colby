"use strict";

var CBPageEditor = {
    model               : null,

    /**
     * @param {Object} spec
     *
     * @return undefined
     */
    displayEditor : function(args) {
        var main = document.getElementsByTagName("main")[0];
        main.textContent = null;

        main.appendChild(CBPageEditor.createEditor({
            spec : args.spec
        }));

        if (window.CBPageEditor2 !== undefined) {
            main.appendChild(CBPageEditor2.createEditor({
                spec : args.spec
            }));
        }
    },

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
 * @param {Object} spec
 *
 * @return undefined
 */
CBPageEditor.createEditor = function(args) {
    var editorContainer = document.createElement("div");
    editorContainer.classList.add("CBPageEditor");

    /**
     * Page information
     */

    var element = CBPageInformationEditorFactory.createEditor({
        handleSpecChanged       : function() { CBPageEditor.requestSave(); },
        handleTitleChanged      : CBPageEditor.handleTitleChanged.bind(undefined, {
            spec                : args.spec
        }),
        spec                    : args.spec });

    editorContainer.appendChild(element);

    /**
     *
     */

    editorContainer.appendChild(CBSpecArrayEditorFactory.createEditor({
        array           : args.spec.sections,
        classNames      : CBPageEditorAvailableViewClassNames,
        handleChanged   : CBPageEditor.requestSave.bind(CBPageEditor)
    }));

    CBPageEditor.handleTitleChanged({spec : args.spec});

    return editorContainer;
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
 * @return undefined
 */
CBPageEditor.DOMContentDidLoad = function() {
    CBPageEditor.saveModelTimer = Object.create(CBDelayTimer).init();
    CBPageEditor.saveModelTimer.callback = CBPageEditor.saveModel.bind(CBPageEditor);
    CBPageEditor.saveModelTimer.delayInMilliseconds = 2000;

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

            CBPageEditor.displayEditor({ spec : CBPageEditor.model });
        } else {
            CBPageEditor.displayPageTemplateChooser();
        }
    } else {
        Colby.displayResponse(response);
    }
};


/**
 * @param {hex160} ID
 * @return undefined
 */
CBPageEditor.makeFrontPage = function(args) {
    var formData = new FormData();
    formData.append("dataStoreID", args.ID);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/pages/api/make-front-page/", true);
    xhr.onload = CBPageEditor.makeFrontPageDidComplete.bind(undefined, { xhr : xhr });
    xhr.send(formData);
};

/**
 * @param {XMLHttpRequest} xhr
 *
 * @return undefined
 */
CBPageEditor.makeFrontPageDidComplete = function(args) {
    var response    = Colby.responseFromXMLHttpRequest(args.xhr);
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
 * timer.
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

document.addEventListener("DOMContentLoaded", CBPageEditor.DOMContentDidLoad);

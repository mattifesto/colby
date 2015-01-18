"use strict";

/**
 * @param {Object} args
 *  The parameters for this functions should be passed as values on this object.
 *
 * @param {Object} model
 *  Required. The page model.
 */
function createPageInformationEditorElement(args) {

    var model = args.model;

    args = undefined;

    var view    = new CBPageInformationEditorView(model);
    var element = view.element();

    return element;
}

/**
 * This class displays and implements the first section of every page editor
 * which is the page information section.
 */
function CBPageInformationEditorView(pageModel)
{
    this.pageModel      = pageModel;

    if (!this.pageModel.URI)
    {
        this.pageModel.URI = this.generateURI();
    }

    this._element               = document.createElement("section");

    this._element.classList.add("CBSectionEditorView");
    this._element.classList.add("CBPageInformationEditorView");

    this._element.appendChild(createHeaderElement());

    var propertiesContainer = createPropertiesContainerElement();
    this._element.appendChild(propertiesContainer);

    /**
     *
     */

    var pageListsEditor = this.createPageListsEditor();
    this._element.appendChild(pageListsEditor);

    /**
     *
     */

    var titleControl = new CBTextControl("Title");
    titleControl.rootElement().classList.add("standard");

    titleControl.setValue(this.pageModel.title);
    titleControl.setAction(this, this.translateTitle);

    propertiesContainer.appendChild(titleControl.rootElement());


    /**
     *
     */

    var descriptionControl = new CBTextControl("Description");
    descriptionControl.setValue(this.pageModel.description);
    descriptionControl.setAction(this, this.translateDescription);

    descriptionControl.rootElement().classList.add("standard");
    propertiesContainer.appendChild(descriptionControl.rootElement());


    /**
     *
     */

    var URI         = this.pageModel.URI ? this.pageModel.URI : this.generateURI();
    var URIControl  = new CBPageURIControl("URI");
    URIControl.setURI(URI);
    URIControl.setIsStatic(this.pageModel.URIIsStatic);
    URIControl.setIsDisabled(this.pageModel.isPublished);
    URIControl.setAction(this, this.translateURI);

    URIControl.rootElement().classList.add("standard");
    propertiesContainer.appendChild(URIControl.rootElement());
    this.URIControl = URIControl;

    /**
     *
     */

    var publicationControl = new CBPublicationControl();
    publicationControl.setPublicationTimeStamp(this.pageModel.publicationTimeStamp);
    publicationControl.setIsPublished(this.pageModel.isPublished);
    publicationControl.setAction(this, this.translatePublication);

    publicationControl.rootElement().classList.add("standard");
    propertiesContainer.appendChild(publicationControl.rootElement());


    /**
     *
     */

    var publishedByContainer = document.createElement("div");
    publishedByContainer.classList.add("container");

    propertiesContainer.appendChild(publishedByContainer);


    /**
     *
     */

    var publishedByControl = new CBSelectionControl("Published By");
    publishedByControl.rootElement().classList.add("standard");
    publishedByControl.rootElement().classList.add("published-by");

    for (var i = 0; i < CBUsersWhoAreAdministrators.length; i++)
    {
        var user = CBUsersWhoAreAdministrators[i];

        publishedByControl.appendOption(user.ID, user.name);
    }

    publishedByControl.setValue(this.pageModel.publishedBy);
    publishedByControl.setAction(this, this.translatePublishedBy);

    publishedByContainer.appendChild(publishedByControl.rootElement());

    /**
     * This timer requests the updated URI after 1000ms of inactivity.
     */

    this.requestURITimer                        = Object.create(CBDelayTimer).init();
    this.requestURITimer.callback               = this.requestURI.bind(this);
    this.requestURITimer.delayInMilliseconds    = 1000;

    /**
     * If we don't have a row ID yet, we can't request a URI. Wait for the row
     * ID to be assigend and then allow URI requests.
     */

    if (!this.pageModel.rowID)
    {
        this.requestURITimer.pause();

        var listener = this.pageRowWasCreated.bind(this);

        document.addEventListener("CBPageRowWasCreated", listener, false);
    }

    /**
     * @return {Element}
     */
    function createHeaderElement() {
        var header          = document.createElement("header");
        header.textContent  = "Page Information";

        return header;
    }

    /**
     * @return {Element}
     */
    function createPropertiesContainerElement() {
        var container       = document.createElement("div");
        container.className = "CBPageInformationProperties";

        return container;
    }
}

/**
 * @return void
 */
CBPageInformationEditorView.prototype.checkboxDidChangeForListClassName = function(checkbox, listClassName) {

    var model = this.pageModel;

    if (!model.listClassNames)
    {
        model.listClassNames = [];
    }

    var index = model.listClassNames.indexOf(listClassName);

    if (checkbox.checked) {

        if (index < 0) {

            model.listClassNames.push(listClassName);
        }

    } else {

        if (index >= 0) {

            model.listClassNames.splice(index, 1);
        }
    }

    CBPageEditor.requestSave();
};

/**
 * @return Element
 */
CBPageInformationEditorView.prototype.createPageListsEditor = function() {

    var element         = document.createElement("div");
    element.className   = "CBPageInformationPageListsEditor";

    var countOfListClassNames = CBPageEditorAvailablePageListClassNames.length;

    for (var i = 0; i < countOfListClassNames; i++) {

        element.appendChild(this.createPageListOption(CBPageEditorAvailablePageListClassNames[i]));
    }

    return element;
};

/**
 * @return Element
 */
CBPageInformationEditorView.prototype.createPageListOption = function(listClassName) {

    var container   = document.createElement("div");
    var checkbox    = document.createElement("input");
    checkbox.type   = "checkbox";
    var label       = document.createElement("label");
    label.textContent   = listClassName;

    container.appendChild(checkbox);
    container.appendChild(label);

    if (this.pageModel.listClassNames) {

        var index = this.pageModel.listClassNames.indexOf(listClassName);

        if (index >= 0) {

            checkbox.checked = true;
        }
    }

    var listener = this.checkboxDidChangeForListClassName.bind(this, checkbox, listClassName);
    checkbox.addEventListener("change", listener);

    return container;
};

/**
 * This function generates a URI for the page using the page group prefix and
 * the current page title. It does not change the model or take into account
 * whether the user has set the page URI to be static.
 *
 * @return string
 */
CBPageInformationEditorView.prototype.generateURI = function()
{
        var groupID = this.pageModel.groupID;
        var URI     = "";

        if (groupID && CBPageGroupDescriptors[groupID])
        {
            var URIPrefix = CBPageGroupDescriptors[groupID].URIPrefix;

            URI = URIPrefix + "/";
        }

        if (this.pageModel.title.length > 0)
        {
            URI = URI + Colby.textToURI(this.pageModel.title);
        }
        else
        {
            URI = URI + this.pageModel.dataStoreID;
        }

        return URI;
};

/**
 * @return void
 */
CBPageInformationEditorView.prototype.element = function()
{
    return this._element;
};

/**
 * If there is no row ID in the model when this object is initialized then
 * this the URI request timer will be paused and this function will be called
 * when the row ID is created to resume the timer.
 *
 * @return void
 */
CBPageInformationEditorView.prototype.pageRowWasCreated = function()
{
    this.requestURITimer.resume();
};

/**
 * @return void
 */
CBPageInformationEditorView.prototype.translatePageGroup = function(sender)
{
    this.pageModel.groupID = sender.value() ? sender.value() : null;

    if (!this.pageModel.URIIsStatic)
    {
        var URI     = this.generateURI();

        this.pageModel.URI = URI;
        this.URIControl.setURI(URI);
    }

    CBPageEditor.requestSave();
};


/**
 * @return void
 */
CBPageInformationEditorView.prototype.translatePublication = function(sender)
{
    this.pageModel.isPublished = sender.isPublished();
    this.pageModel.publicationTimeStamp = sender.publicationTimeStamp();

    if (this.pageModel.isPublished)
    {
        this.pageModel.URIIsStatic = true;

        this.URIControl.setIsStatic(true);
    }

    this.URIControl.setIsDisabled(this.pageModel.isPublished);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBPageInformationEditorView.prototype.translateDescription = function(sender)
{
    this.pageModel.description = sender.value().trim();
    this.pageModel.descriptionHTML = Colby.textToHTML(this.pageModel.description);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBPageInformationEditorView.prototype.translatePublishedBy = function(sender)
{
    this.pageModel.publishedBy = parseInt(sender.value(), 10);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBPageInformationEditorView.prototype.translateTitle = function(sender)
{
    this.pageModel.title = sender.value().trim();
    this.pageModel.titleHTML = Colby.textToHTML(this.pageModel.title);

    if (!this.pageModel.URIIsStatic)
    {
        var URI     = this.generateURI();

        this.URIControl.setURI(URI);
        this.translateURI(this.URIControl);
    }

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBPageInformationEditorView.prototype.translateURI = function(sender)
{
    this.pageModel.URIIsStatic  = sender.isStatic();
    this.proposedURI            = sender.URI();

    this.requestURITimer.restart();

    CBPageEditor.requestSave();
};

/**
 * This function should never be called directly. It should only be called as
 * the `requestURITimer` object's callback.
 *
 * @return void
 */
CBPageInformationEditorView.prototype.requestURI = function()
{
    var formData = new FormData();

    formData.append("rowID", this.pageModel.rowID);
    formData.append("URI", this.proposedURI);

    var xhr     = new XMLHttpRequest();
    xhr.onload  = this.requestURIDidComplete.bind(this, xhr);

    xhr.open("POST", "/admin/pages/api/request-uri/", true);
    xhr.send(formData);

    this.URIControl.textField.style.backgroundColor = "#fffff0";

    /**
     * Prevent another callback while the URI is being requested.
     */

    this.requestURITimer.pause();
};

/**
 * @return void
 */
CBPageInformationEditorView.prototype.requestURIDidComplete = function(xhr)
{
    /**
     * Re-enable callbacks.
     */

    this.requestURITimer.resume();

    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
    else if (response.URIWasGranted)
    {
        this.pageModel.URI                              = response.URI;
        this.URIControl.textField.style.backgroundColor = "white";

        CBPageEditor.requestSave();
    }
    else
    {
        this.URIControl.textField.style.backgroundColor = "#fff0f0";
    }
};

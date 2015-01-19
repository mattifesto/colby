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

    var proposedURI;

    /**
     * export functions (temporary)
     */

    this.generateURI                = generateURI;
    this.valuesForURIHaveChanged    = translateURI;

    if (!this.pageModel.URI)
    {
        this.pageModel.URI = generateURI();
    }

    this._element = createRootElement();

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

    propertiesContainer.appendChild(createDescriptionControlElement());


    /**
     *
     */

    var URI         = this.pageModel.URI ? this.pageModel.URI : generateURI();
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

    propertiesContainer.appendChild(createPublicationControlElement());


    /**
     *
     */

    var publishedByContainer = document.createElement("div");
    publishedByContainer.classList.add("container");

    propertiesContainer.appendChild(publishedByContainer);


    /**
     *
     */

    publishedByContainer.appendChild(createPublishedByControlElement());


    /**
     * This timer requests the updated URI after 1000ms of inactivity.
     */

    var requestURITimer                 = Object.create(CBDelayTimer).init();
    requestURITimer.callback            = requestURI;
    requestURITimer.delayInMilliseconds = 1000;

    /**
     * If we don't have a row ID yet, we can't request a URI. Wait for the row
     * ID to be assigend and then allow URI requests.
     */

    if (!pageModel.rowID)
    {
        requestURITimer.pause();

        document.addEventListener("CBPageRowWasCreated", pageRowWasCreated, false);
    }

    /**
     * @return {Element}
     */
    function createDescriptionControlElement() {
        var descriptionControl = new CBTextControl("Description");

        descriptionControl.setValue(pageModel.description);
        descriptionControl.setAction(undefined, valueForDescriptionHasChanged);
        descriptionControl.rootElement().classList.add("standard");

        return descriptionControl.rootElement();
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

    /**
     * @return {Element}
     */
    function createPublicationControlElement() {
        var control = new CBPublicationControl();

        control.setPublicationTimeStamp(pageModel.publicationTimeStamp);
        control.setIsPublished(pageModel.isPublished);
        control.setAction(undefined, valuesForPublicationHaveChanged);
        control.rootElement().classList.add("standard");

        return control.rootElement();
    }

    /**
     * @return {Element}
     */
    function createPublishedByControlElement() {
        var control = new CBSelectionControl("Published By");

        control.rootElement().classList.add("standard");
        control.rootElement().classList.add("published-by");

        for (var i = 0; i < CBUsersWhoAreAdministrators.length; i++) {
            var user = CBUsersWhoAreAdministrators[i];

            control.appendOption(user.ID, user.name);
        }

        control.setValue(pageModel.publishedBy);
        control.setAction(undefined, valueForPublishedByHasChanged);

        return control.rootElement();
    }

    /**
     * @return {Element}
     */
    function createRootElement() {
        var rootElement = document.createElement("section");

        rootElement.classList.add("CBSectionEditorView");
        rootElement.classList.add("CBPageInformationEditorView");

        return rootElement;
    }

    /**
     * @return {string}
     */
    function generateURI() {
        var URI;

        if (pageModel.title.length > 0) {
            URI = Colby.textToURI(pageModel.title);
        } else {
            URI = pageModel.dataStoreID;
        }

        return URI;
    }

    /**
     * If there is no row ID in the model when this object is initialized then
     * this the URI request timer will be paused and this function will be called
     * when the row ID is created to resume the timer.
     *
     * @return {undefined}
     */
    function pageRowWasCreated() {
        requestURITimer.resume();
    }

    /**
     * This function is the callback for the `requestURITimer`.
     *
     * @return {undefined}
     */
    function requestURI() {
        var formData = new FormData();

        formData.append("rowID", pageModel.rowID);
        formData.append("URI", proposedURI);

        var xhr     = new XMLHttpRequest();
        xhr.onload  = requestURIDidComplete.bind(undefined, xhr);
        xhr.open("POST", "/admin/pages/api/request-uri/", true);
        xhr.send(formData);

        URIControl.textField.style.backgroundColor = "#fffff0";

        /**
         * Prevent another callback while the URI is being requested.
         */
        requestURITimer.pause();
    }

    /**
     * @return {undefined}
     */
    function requestURIDidComplete(xhr) {

        /**
         * Because the request is complete further requests can be processed.
         */
        requestURITimer.resume();

        var response = Colby.responseFromXMLHttpRequest(xhr);

        if (!response.wasSuccessful)
        {
            Colby.displayResponse(response);
        }
        else if (response.URIWasGranted)
        {
            pageModel.URI                               = response.URI;
            URIControl.textField.style.backgroundColor  = "white";

            CBPageEditor.requestSave();
        }
        else
        {
            URIControl.textField.style.backgroundColor  = "#fff0f0";
        }
    }

    /**
     * @param {CBTextControl} sender
     *
     * @return {undefined}
     */
    function valueForDescriptionHasChanged(sender) {
        pageModel.description       = sender.value().trim();
        pageModel.descriptionHTML   = Colby.textToHTML(pageModel.description);

        CBPageEditor.requestSave();
    }

    /**
     * @param {CBSelectionControl} sender
     *
     * @return {undefined}
     */
    function valueForPublishedByHasChanged(sender) {
        pageModel.publishedBy = parseInt(sender.value(), 10);

        CBPageEditor.requestSave();
    }

    /**
     * @param {CBPublicationControl} sender
     *
     * @return {undefined}
     */
    function valuesForPublicationHaveChanged(sender) {
        pageModel.isPublished = sender.isPublished();
        pageModel.publicationTimeStamp = sender.publicationTimeStamp();

        if (pageModel.isPublished)
        {
            pageModel.URIIsStatic = true;

            URIControl.setIsStatic(true);
        }

        URIControl.setIsDisabled(pageModel.isPublished);

        CBPageEditor.requestSave();
    }

    /**
     * @param {URIControl} sender
     *
     * @return {undefined}
     */
    function valuesForURIHaveChanged(sender) {
        pageModel.URIIsStatic   = sender.isStatic();
        proposedURI             = sender.URI();

        requestURITimer.restart();

        CBPageEditor.requestSave();
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
 * @return void
 */
CBPageInformationEditorView.prototype.element = function()
{
    return this._element;
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
        this.valuesForURIHaveChanged(this.URIControl);
    }

    CBPageEditor.requestSave();
};

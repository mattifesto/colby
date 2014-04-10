"use strict";


/**
 * This class displays and implements the first section of every page editor
 * which is the page information section.
 */
function CBPageInformationEditorView(pageModel)
{
    var self = this;

    this.pageModel      = pageModel;

    if (!this.pageModel.URI)
    {
        this.pageModel.URI = this.generateURI();
    }

    this._element   = document.createElement("section");
    var header      = document.createElement("header");
    var headerTitle = document.createTextNode("Page Information");
    this._container = document.createElement("div");

    this._element.classList.add("CBSectionEditorView");
    this._element.classList.add("CBPageInformationEditorView");
    this._element.appendChild(header);
    header.appendChild(headerTitle);
    this._element.appendChild(this._container);


    /**
     *
     */

    var titleControl = new CBTextControl("Title");
    titleControl.rootElement().classList.add("standard");

    titleControl.setValue(this.pageModel.title);
    titleControl.setAction(this, this.translateTitle);

    this._container.appendChild(titleControl.rootElement());


    /**
     *
     */

    var descriptionControl = new CBTextControl("Description");
    descriptionControl.setValue(this.pageModel.description);
    descriptionControl.setAction(this, this.translateDescription);

    descriptionControl.rootElement().classList.add("standard");
    this._container.appendChild(descriptionControl.rootElement());


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
    this._container.appendChild(URIControl.rootElement());
    this.URIControl = URIControl;

    if (!this.pageModel.rowID)
    {
        var self    = this;
        var handler = function()
        {
            self.translateURI(URIControl);
        };

        document.addEventListener("CBPageRowWasCreated", handler, false);
    }

    /**
     *
     */

    var publicationControl = new CBPublicationControl();
    publicationControl.setPublicationTimeStamp(this.pageModel.publicationTimeStamp);
    publicationControl.setIsPublished(this.pageModel.isPublished);
    publicationControl.setAction(this, this.translatePublication);

    publicationControl.rootElement().classList.add("standard");
    this._container.appendChild(publicationControl.rootElement());


    /**
     *
     */

    var container = document.createElement("div");
    container.classList.add("container");

    this._container.appendChild(container);


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

    container.appendChild(publishedByControl.rootElement());


    /**
     *
     */

    var pageGroupControl = new CBSelectionControl("Page Group");
    pageGroupControl.rootElement().classList.add("standard");
    pageGroupControl.rootElement().classList.add("page-group");

    pageGroupControl.appendOption("", "None");

    for (var ID in CBPageGroupDescriptors)
    {
        pageGroupControl.appendOption(ID, CBPageGroupDescriptors[ID].name);
    }

    pageGroupControl.setValue(this.pageModel.groupID);
    pageGroupControl.setAction(this, this.translatePageGroup);

    container.appendChild(pageGroupControl.rootElement());

    /**
     *
     */

    this._requestURIAjaxRequest         = new CBContinuousAjaxRequest();
    this._requestURIAjaxRequest.delay   = 1000;
    this._requestURIAjaxRequest.onload  = this.requestURIDidComplete.bind(this);
    this._requestURIAjaxRequest.URL     = "/admin/pages/api/request-uri/";
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
}

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
    if (!this.pageModel.rowID)
    {
        CBPageEditor.requestCreatePageRow();

        var self    = this;
        var handler = function()
        {
            self.translateURI(sender);
        }

        document.addEventListener("CBPageRowWasCreated", handler, false);
    }

    var formData = new FormData();
    formData.append("rowID", this.pageModel.rowID);
    formData.append("URI", sender.URI());
    this._requestURIAjaxRequest.makeRequestWithFormData(formData);

    this.pageModel.URIIsStatic = sender.isStatic();

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBPageInformationEditorView.prototype.requestURIDidComplete = function(xhr)
{
    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
    else if (response.URIWasGranted)
    {
        this.pageModel.URI = response.URI;

        CBPageEditor.requestSave();
    }
    else
    {
        // TODO: Provide visual indication that the URI request wasn't granted.
    }
};

"using strict";

/**
 * This class displays and implements the first section of every page editor
 * which is the page information section.
 */
function CBPageInformation(pageModel, sectionModel, sectionElement)
{
    var self = this;

    this.pageModel      = pageModel;
    this.sectionElement = sectionElement;

    if (!this.pageModel.URI)
    {
        this.pageModel.URI = this.generateURI();
    }

    this.display();
}

CBPageInformation.schema = "CBPageInformation";

/**
 * @return void
 */
CBPageInformation.prototype.display = function()
{
    /**
     *
     */

    var titleControl = new CBTextControl("Title");
    titleControl.rootElement().classList.add("standard");

    titleControl.setValue(this.pageModel.title);
    titleControl.setAction(this, this.translateTitle);

    this.sectionElement.appendChild(titleControl.rootElement());


    /**
     *
     */

    var descriptionControl = new CBTextControl("Description");
    descriptionControl.setValue(this.pageModel.description);
    descriptionControl.setAction(this, this.translateDescription);

    descriptionControl.rootElement().classList.add("standard");
    this.sectionElement.appendChild(descriptionControl.rootElement());


    /**
     *
     */

    var URIControl = new CBPageURIControl("URI");
    URIControl.setURI(this.pageModel.URI);
    URIControl.setIsStatic(this.pageModel.URIIsStatic);
    URIControl.setIsDisabled(this.pageModel.isPublished);
    URIControl.setAction(this, this.translateURI);

    URIControl.rootElement().classList.add("standard");
    this.sectionElement.appendChild(URIControl.rootElement());
    this.URIControl = URIControl;

    /**
     *
     */

    var publicationControl = new CBPublicationControl();
    publicationControl.setPublicationTimeStamp(this.pageModel.publicationTimeStamp);
    publicationControl.setIsPublished(this.pageModel.isPublished);
    publicationControl.setAction(this, this.translatePublication);

    publicationControl.rootElement().classList.add("standard");
    this.sectionElement.appendChild(publicationControl.rootElement());


    /**
     *
     */

    var container = document.createElement("div");
    container.classList.add("container");

    this.sectionElement.appendChild(container);


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

    for (ID in CBPageGroupDescriptors)
    {
        pageGroupControl.appendOption(ID, CBPageGroupDescriptors[ID].name);
    }

    pageGroupControl.setValue(this.pageModel.groupID);
    pageGroupControl.setAction(this, this.translatePageGroup);

    container.appendChild(pageGroupControl.rootElement());
};

/**
 * This function generates a URI for the page using the page group prefix and
 * the current page title. It does not change the model or take into account
 * whether the user has set the page URI to be static.
 *
 * @return string
 */
CBPageInformation.prototype.generateURI = function()
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
CBPageInformation.prototype.translatePageGroup = function(sender)
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
CBPageInformation.prototype.translatePublication = function(sender)
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
CBPageInformation.prototype.translateDescription = function(sender)
{
    this.pageModel.description = sender.value().trim();
    this.pageModel.descriptionHTML = Colby.textToHTML(this.pageModel.description);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBPageInformation.prototype.translateTitle = function(sender)
{
    this.pageModel.title = sender.value().trim();
    this.pageModel.titleHTML = Colby.textToHTML(this.pageModel.title);

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
CBPageInformation.prototype.translateURI = function(sender)
{
    this.pageModel.URI          = sender.URI();
    this.pageModel.URIIsStatic  = sender.isStatic();

    CBPageEditor.requestSave();
};


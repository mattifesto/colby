"use strict";


/**
 *
 */
function CBBackgroundSectionEditor(pageModel, sectionModel)
{
    this._pageModel     = pageModel;
    this._sectionModel  = sectionModel;
    this._element       = document.createElement("div");
    this._element.classList.add("section-type-" + sectionModel.sectionTypeID);

    var container                  = document.createElement("div");
    container.style.textAlign      = "center";
    container.style.marginBottom   = "10px";
    container.style.marginTop      = "10px";
    this._element.appendChild(container);

    var fileControl    = new CBFileLinkControl("upload background image");
    fileControl.setAction(this, this.translateImage);
    container.appendChild(fileControl.rootElement());

    var container                  = document.createElement("div");
    container.style.textAlign      = "center";
    container.style.marginBottom   = "10px";
    container.style.marginTop      = "10px";
    this._element.appendChild(container);

    var repeatHorizontallyCheckbox = new CBCheckboxControl("Repeat horizontally");
    repeatHorizontallyCheckbox.setIsChecked(sectionModel.imageRepeatHorizontally);
    repeatHorizontallyCheckbox.setAction(this, this.translateRepeatHorizontally);
    container.appendChild(repeatHorizontallyCheckbox.rootElement());

    var repeatVerticallyCheckbox = new CBCheckboxControl("Repeat vertically");
    repeatVerticallyCheckbox.setIsChecked(sectionModel.imageRepeatVertically);
    repeatVerticallyCheckbox.setAction(this, this.translateRepeatVertically);
    repeatVerticallyCheckbox.rootElement().style.marginLeft = "2em";
    container.appendChild(repeatVerticallyCheckbox.rootElement());

    var minimumSectionHeightIsImageHeightCheckbox = new CBCheckboxControl("Minimum section height is image height");
    minimumSectionHeightIsImageHeightCheckbox.setIsChecked(sectionModel.minimumSectionHeightIsImageHeight);
    minimumSectionHeightIsImageHeightCheckbox.setAction(this, this.translateMinimumSectionHeightIsImageHeight);
    minimumSectionHeightIsImageHeightCheckbox.rootElement().style.marginLeft = "2em";
    container.appendChild(minimumSectionHeightIsImageHeightCheckbox.rootElement());

    var backgroundColorControl = new CBTextControl("Background color");
    backgroundColorControl.rootElement().classList.add("standard");
    backgroundColorControl.setValue(sectionModel.backgroundColor);
    backgroundColorControl.setAction(this, this.translateBackgroundColor);
    this._element.appendChild(backgroundColorControl.rootElement());

    var linkURLControl = new CBTextControl("Link URL");
    linkURLControl.rootElement().classList.add("standard");
    linkURLControl.setValue(sectionModel.linkURL);
    linkURLControl.setAction(this, this.translateLinkURL);
    this._element.appendChild(linkURLControl.rootElement());

    var childContainer          = document.createElement("div");
    var childContainerHeader    = document.createElement("header");
    var childListView           = new CBSectionListView(sectionModel.children);

    childContainer.classList.add("children");

    childContainerHeader.appendChild(document.createTextNode("Background Child Sections"));
    childContainer.appendChild(childContainerHeader);
    childContainer.appendChild(childListView.element());
    this._element.appendChild(childContainer);

    this.updateThumbnail();
}

/**
 * @return Element
 */
CBBackgroundSectionEditor.prototype.element = function()
{
    return this._element;
};

/**
 * @return void
 */
CBBackgroundSectionEditor.prototype.translateBackgroundColor = function(sender)
{
    this._sectionModel.backgroundColor = sender.value().trim();

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundSectionEditor.prototype.translateImage = function(sender)
{
    if (!this._request)
    {
        this._request = new CBContinuousAjaxRequest("/admin/pages/api/upload-image/");

        var self = this;

        var handler = function(xhr)
        {
            self.translateImageDidComplete(xhr);
        };

        this._request.onload = handler;
    }

    var formData = new FormData();
    formData.append("dataStoreID", this._pageModel.dataStoreID);
    formData.append("image", sender.files()[0]);

    this._request.makeRequestWithFormData(formData);
};

/**
 * @return void
 */
CBBackgroundSectionEditor.prototype.translateImageDidComplete = function(xhr)
{
    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
    else
    {
        this._sectionModel.imageFilename    = response.imageFilename;
        this._sectionModel.imageSizeX       = response.imageSizeX;
        this._sectionModel.imageSizeY       = response.imageSizeY;
        this.updateThumbnail();

        CBPageEditor.requestSave();
    }
};

/**
 * @return void
 */
CBBackgroundSectionEditor.prototype.translateLinkURL = function(sender)
{
    this._sectionModel.linkURL      = sender.value().trim();
    this._sectionModel.linkURLHTML  = Colby.textToHTML(this._sectionModel.linkURL);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundSectionEditor.prototype.translateMinimumSectionHeightIsImageHeight = function(sender)
{
    this._sectionModel.minimumSectionHeightIsImageHeight = sender.isChecked();

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundSectionEditor.prototype.translateRepeatHorizontally = function(sender)
{
    this._sectionModel.imageRepeatHorizontally = sender.isChecked();

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundSectionEditor.prototype.translateRepeatVertically = function(sender)
{
    this._sectionModel.imageRepeatVertically = sender.isChecked();

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundSectionEditor.prototype.updateThumbnail = function()
{
    if (!this._sectionModel.imageFilename)
    {
        return;
    }

    if (!this._thumbnail)
    {
        this._thumbnail = document.createElement("img");
        this._element.insertBefore(this._thumbnail, this._element.firstChild);
    }

    var URI = Colby.dataStoreIDToURI(this._pageModel.dataStoreID);
    URI     = URI + "/" + this._sectionModel.imageFilename;

    this._thumbnail.src = URI;
};

/**
 * @return void
 */
CBBackgroundSectionEditor.register = function()
{
    CBPageEditor.registerSectionEditor(CBBackgroundSectionTypeID, CBBackgroundSectionEditor);
};

document.addEventListener("CBPageEditorDidLoad", CBBackgroundSectionEditor.register, false);

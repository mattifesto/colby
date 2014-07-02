"use strict";


var CBBackgroundViewEditor = Object.create(CBViewEditor);

/**
 * @return CBBackgroundViewEditor
 */
CBBackgroundViewEditor.init = function()
{
    CBViewEditor.init.call(this);

    this.model.className = "CBBackgroundView";

    this.model.children                         = [];
    this.model.color                            = null;
    this.model.colorHTML                        = null;
    this.model.imageHeight                      = null;
    this.model.imageShouldRepeatHorizontally    = false;
    this.model.imageShouldRepeatVertically      = false;
    this.model.imageURL                         = null;
    this.model.imageURLHTML                     = null;
    this.model.imageWidth                       = null;
    this.model.linkURL                          = null;
    this.model.linkURLHTML                      = null;
    this.model.minimumViewHeightIsImageHeight   = true;

    return this;
};

/**
 * @return CBBackgroundViewEditor
 */
CBBackgroundViewEditor.initWithModel = function(model)
{
    if ("c4bacd7cf5315e5a07c20072cbb0f355bdb4b8bc" == model.sectionTypeID)
    {
        var dataStoreURI = Colby.dataStoreIDToURI(CBPageEditor.model.dataStoreID);

        this.init();

        this.model.children                         = model.children;
        this.model.color                            = model.backgroundColor;
        this.model.colorHTML                        = Colby.textToHTML(model.backgroundColor);
        this.model.imageHeight                      = model.imageSizeY;
        this.model.imageShouldRepeatHorizontally    = model.imageRepeatHorizontally;
        this.model.imageShouldRepeatVertically      = model.imageRepeatVertically;

        if (model.imageFilename)
        {
            this.model.imageURL                         = dataStoreURI + "/" + model.imageFilename;
            this.model.imageURLHTML                     = Colby.textToHTML(this.model.imageURL);
        }

        this.model.imageWidth                       = model.imageSizeX;
        this.model.linkURL                          = model.linkURL;
        this.model.linkURLHTML                      = model.linkURLHTML;
        this.model.minimumViewHeightIsImageHeight   = model.minimumSectionHeightIsImageHeight;
    }
    else
    {
        CBViewEditor.initWithModel.call(this, model);
    }

    return this;
};

/**
 * @return void
 */
CBBackgroundViewEditor.createElement = function()
{
    this._element           = document.createElement("div");
    this._element.className = "CBBackgroundViewEditor";

    this.createUploadBackgroundImageButton();
    this.createOptionsElement();
    this.createBackgroundColorTextField();
    this.createLinkURLTextField();
    this.createChildViewsElement();

    this.updateBackgroundImageThumbnail();
};

/**
 * @return void
 */
CBBackgroundViewEditor.createBackgroundColorTextField = function()
{
    var containerElement        = document.createElement("div");
    containerElement.className  = "text-field";
    var ID                      = "id-" + Colby.random160();
    var inputElement            = document.createElement("input");
    inputElement.id             = ID;
    inputElement.type           = "text";
    inputElement.value          = this.model.color;
    var labelElement            = document.createElement("label");
    labelElement.htmlFor        = ID;
    labelElement.textContent    = "Background color";
    var callback                = this.propertyBackgroundColorDidChange.bind(this, inputElement);

    inputElement.addEventListener("input", callback);

    containerElement.appendChild(labelElement);
    containerElement.appendChild(inputElement);
    this._element.appendChild(containerElement);
};

/**
 * @return void
 */
CBBackgroundViewEditor.createChildViewsElement = function()
{
    var childViewsElement               = document.createElement("div");
    childViewsElement.className         = "children";
    var childViewsTitleElement          = document.createElement("h1");
    childViewsTitleElement.textContent  = "CBBackgroundView Child Views";
    var childListView                   = new CBSectionListView(this.model.children);

    childViewsElement.appendChild(childViewsTitleElement);
    childViewsElement.appendChild(childListView.element());
    this._element.appendChild(childViewsElement);
};

/**
 * @return void
 */
CBBackgroundViewEditor.createLinkURLTextField = function()
{
    var containerElement        = document.createElement("div");
    containerElement.className  = "text-field";
    var ID                      = "id-" + Colby.random160();
    var inputElement            = document.createElement("input");
    inputElement.id             = ID;
    inputElement.type           = "text";
    inputElement.value          = this.model.linkURL;
    var labelElement            = document.createElement("label");
    labelElement.htmlFor        = ID;
    labelElement.textContent    = "Link URL";
    var callback                = this.propertyLinkURLDidChange.bind(this, inputElement);

    inputElement.addEventListener("input", callback);

    containerElement.appendChild(labelElement);
    containerElement.appendChild(inputElement);
    this._element.appendChild(containerElement);
};

/**
 * @return void
 */
CBBackgroundViewEditor.createMinimumViewHeightIsImageHeightOption = function()
{
    var ID              = "id-" + Colby.random160();
    var checkbox        = document.createElement("input");
    checkbox.checked    = this.model.minimumViewHeightIsImageHeight;
    checkbox.id         = ID;
    checkbox.type       = "checkbox";
    var label           = document.createElement("label");
    label.htmlFor       = ID;
    label.textContent   = " Minimum view height is image height";
    var callback        = this.propertyMinimumViewHeightIsImageHeightDidChange.bind(this, checkbox);

    checkbox.addEventListener("change", callback);

    this._optionsElement.appendChild(checkbox);
    this._optionsElement.appendChild(label);
};

/**
 * @return void
 */
CBBackgroundViewEditor.createOptionsElement = function()
{
    this._optionsElement            = document.createElement("div");
    this._optionsElement.className  = "options";

    this.createRepeatHorizontallyOption();
    this.createRepeatVerticallyOption();
    this.createMinimumViewHeightIsImageHeightOption();

    this._element.appendChild(this._optionsElement);
};

/**
 * @return void
 */
CBBackgroundViewEditor.createRepeatHorizontallyOption = function()
{
    var ID              = "id-" + Colby.random160();
    var checkbox        = document.createElement("input");
    checkbox.checked    = this.model.imageShouldRepeatHorizontally;
    checkbox.id         = ID;
    checkbox.type       = "checkbox";
    var label           = document.createElement("label");
    label.htmlFor       = ID;
    label.textContent   = " Repeat horizontally";
    var callback        = this.propertyImageShouldRepeatHorizontallyDidChange.bind(this, checkbox);

    checkbox.addEventListener("change", callback);

    this._optionsElement.appendChild(checkbox);
    this._optionsElement.appendChild(label);
};

/**
 * @return void
 */
CBBackgroundViewEditor.createRepeatVerticallyOption = function()
{
    var ID              = "id-" + Colby.random160();
    var checkbox        = document.createElement("input");
    checkbox.checked    = this.model.imageShouldRepeatVertically;
    checkbox.id         = ID;
    checkbox.type       = "checkbox";
    var label           = document.createElement("label");
    label.htmlFor       = ID;
    label.textContent   = " Repeat vertically";
    var callback        = this.propertyImageShouldRepeatVerticallyDidChange.bind(this, checkbox);

    checkbox.addEventListener("change", callback);

    this._optionsElement.appendChild(checkbox);
    this._optionsElement.appendChild(label);
};

/**
 * @return void
 */
CBBackgroundViewEditor.createUploadBackgroundImageButton = function()
{
    var callback;

    var fileInputElement             = document.createElement("input");
    fileInputElement.style.display   = "none";
    fileInputElement.type            = "file";

    callback = this.propertyBackgroundImageFileDidChange.bind(this, fileInputElement);
    fileInputElement.addEventListener("change", callback);

    var buttonElement           = document.createElement("button");
    buttonElement.textContent   = "Upload Background Image";

    callback = fileInputElement.click.bind(fileInputElement);
    buttonElement.addEventListener("click", callback);

    this._element.appendChild(fileInputElement);
    this._element.appendChild(buttonElement);
};

/**
 * @return Element
 */
CBBackgroundViewEditor.element = function()
{
    if (!this._element)
    {
        this.createElement();
    }

    return this._element;
};

/**
 * @return void
 */
CBBackgroundViewEditor.propertyBackgroundColorDidChange = function(inputElement)
{
    this.model.color        = inputElement.value;
    this.model.colorHTML    = Colby.textToHTML(inputElement.value);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundViewEditor.propertyBackgroundImageFileDidChange = function(backgroundImageFileInputElement)
{
    if (this._backgroundImageUploadXHR)
    {
        this._backgroundImageUploadXHR.abort();
        this._backgroundImageUploadXHR = null;
    }

    var formData    = new FormData();
    formData.append("dataStoreID", CBPageEditor.model.dataStoreID);
    formData.append("image", backgroundImageFileInputElement.files[0]);

    var xhr     = new XMLHttpRequest();
    xhr.onload  = this.propertyBackgroundImageFileDidUpload.bind(this);
    xhr.open("POST", "/admin/pages/api/upload-image/");
    xhr.send(formData);

    this._backgroundImageUploadXHR = xhr;
};

/**
 * @return void
 */
CBBackgroundViewEditor.propertyBackgroundImageFileDidUpload = function()
{
    var response = Colby.responseFromXMLHttpRequest(this._backgroundImageUploadXHR);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
    else
    {
        this.model.imageURL         = response.imageURL;
        this.model.imageURLHTML     = Colby.textToHTML(response.imageURL);
        this.model.imageWidth       = response.imageSizeX;
        this.model.imageHeight      = response.imageSizeY;

        this.updateBackgroundImageThumbnail();

        CBPageEditor.requestSave();
    }
};

/**
 * @return void
 */
CBBackgroundViewEditor.propertyImageShouldRepeatHorizontallyDidChange = function(checkboxElement)
{
    this.model.imageShouldRepeatHorizontally = checkboxElement.checked;

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundViewEditor.propertyImageShouldRepeatVerticallyDidChange = function(checkboxElement)
{
    this.model.imageShouldRepeatVertically = checkboxElement.checked;

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundViewEditor.propertyLinkURLDidChange = function(inputElement)
{
    this.model.linkURL      = inputElement.value;
    this.model.linkURLHTML  = Colby.textToHTML(inputElement.value);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundViewEditor.propertyMinimumViewHeightIsImageHeightDidChange = function(checkboxElement)
{
    this.model.minimumViewHeightIsImageHeight = checkboxElement.checked;

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundViewEditor.updateBackgroundImageThumbnail = function()
{
    if (!this.model.imageURL)
    {
        return;
    }

    if (!this._thumbnail)
    {
        this._imageDimensions = document.createElement("div");
        this._element.insertBefore(this._imageDimensions, this._element.firstChild);
        this._thumbnail = document.createElement("img");
        this._element.insertBefore(this._thumbnail, this._element.firstChild);
    }

    this._imageDimensions.textContent   = this.model.imageWidth + " × " + this.model.imageHeight;
    this._thumbnail.src                 = this.model.imageURL;
};

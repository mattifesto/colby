"use strict";


var CBBackgroundViewEditor = Object.create(CBViewEditor);

/**
 * @return CBBackgroundViewEditor
 */
CBBackgroundViewEditor.init = function()
{
    CBViewEditor.init.call(this);

    this.model.className = "CBBackgroundView";

    this.model.backgroundColor                  = "";
    this.model.backgroundColorHTML              = "";
    this.model.children                         = [];
    this.model.imageFilename                    = null;
    this.model.imageShouldRepeatHorizontally    = false;
    this.model.imageShouldRepeatVertically      = false;
    this.model.imageHeight                      = null;
    this.model.imageWidth                       = null;
    this.model.linkURL                          = "";
    this.model.linkURLHTML                      = "";
    this.model.minimumViewHeightIsImageHeight   = true;

    return this;
}

/**
 * @return void
 */
CBBackgroundViewEditor.backgroundColorDidChange = function(inputElement)
{
    this.model.backgroundColor      = inputElement.value;
    this.model.backgroundColorHTML  = Colby.textToHTML(inputElement.value);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundViewEditor.backgroundImageFileDidChange = function(backgroundImageFileInputElement)
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
    xhr.onload  = this.backgroundImageFileDidUpload.bind(this);
    xhr.open("POST", "/admin/pages/api/upload-image/");
    xhr.send(formData);

    this._backgroundImageUploadXHR = xhr;
};

/**
 * @return void
 */
CBBackgroundViewEditor.backgroundImageFileDidUpload = function()
{
    var response = Colby.responseFromXMLHttpRequest(this._backgroundImageUploadXHR);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
    else
    {
        this.model.imageFilename    = response.imageFilename;
        this.model.imageWidth       = response.imageSizeX;
        this.model.imageHeight      = response.imageSizeY;

        this.updateBackgroundImageThumbnail();

        CBPageEditor.requestSave();
    }
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
    inputElement.value          = this.model.backgroundColor;
    var labelElement            = document.createElement("label");
    labelElement.htmlFor        = ID;
    labelElement.textContent    = "Background color";

    inputElement.addEventListener("input", this.backgroundColorDidChange.bind(this, inputElement));

    containerElement.appendChild(labelElement);
    containerElement.appendChild(inputElement);
    this._element.appendChild(containerElement);
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

    inputElement.addEventListener("input", this.linkURLDidChange.bind(this, inputElement));

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

    checkbox.addEventListener("change", this.minimumViewHeightIsImageHeightDidChange.bind(this, checkbox));

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

    checkbox.addEventListener("change", this.imageShouldRepeatHorizontallyDidChange.bind(this, checkbox));

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

    checkbox.addEventListener("change", this.imageShouldRepeatVerticallyDidChange.bind(this, checkbox));

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

    callback = this.backgroundImageFileDidChange.bind(this, fileInputElement);
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
CBBackgroundViewEditor.imageShouldRepeatHorizontallyDidChange = function(checkboxElement)
{
    this.model.imageShouldRepeatHorizontally = checkboxElement.checked;

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundViewEditor.imageShouldRepeatVerticallyDidChange = function(checkboxElement)
{
    this.model.imageShouldRepeatVertically = checkboxElement.checked;

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundViewEditor.linkURLDidChange = function(inputElement)
{
    this.model.linkURL      = inputElement.value;
    this.model.linkURLHTML  = Colby.textToHTML(inputElement.value);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundViewEditor.minimumViewHeightIsImageHeightDidChange = function(checkboxElement)
{
    this.model.minimumViewHeightIsImageHeight = checkboxElement.checked;

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBBackgroundViewEditor.updateBackgroundImageThumbnail = function()
{
    if (!this.model.imageFilename)
    {
        return;
    }

    if (!this._thumbnail)
    {
        this._thumbnail = document.createElement("img");
        this._element.insertBefore(this._thumbnail, this._element.firstChild);
    }

    var URI             = Colby.dataStoreIDToURI(CBPageEditor.model.dataStoreID);
    this._thumbnail.src = URI + "/" + this.model.imageFilename;
};

"use strict";


var CBBackgroundViewEditor = Object.create(CBViewEditor);

/**
 * @return CBBackgroundViewEditor
 */
CBBackgroundViewEditor.init = function()
{
    CBViewEditor.init.call(this);

    this.model.className = "CBBackgroundView";

    this.model.backgroundColor                     = "";
    this.model.children                            = [];
    this.model.imageFilename                       = null;
    this.model.imageShouldRepeatHorizontally       = false;
    this.model.imageShouldRepeatVertically         = false;
    this.model.imageHeight                         = null;
    this.model.imageWidth                          = null;
    this.model.linkURL                             = "";
    this.model.linkURLHTML                         = "";
    this.model.minimumSectionHeightIsImageHeight   = true;

    return this;
}

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
        //this.updateThumbnail();

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
};

/**
 * @return void
 */
CBBackgroundViewEditor.createOptionsElement = function()
{
    this._optionsElement            = document.createElement("div");
    this._optionsElement.className  = "options";

    this.createRepeatHorizontallyCheckbox();

    this._element.appendChild(this._optionsElement);
};

/**
 * @return void
 */
CBBackgroundViewEditor.createRepeatHorizontallyCheckbox = function()
{
    var checkbox    = document.createElement("input");
    checkbox.type   = "checkbox";

    this._optionsElement.appendChild(checkbox);
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

"use strict";

var CBImageViewEditor = Object.create(CBViewEditor);

Colby.extend(CBImageViewEditor, {

    chromeClass     : "CBImageViewEditorChrome",
    cropToHeight    : null,
    cropToWidth     : null,
    reduceToHeight  : null,
    reduceToWidth   : null
});

/**
 * @return instance type
 */
CBImageViewEditor.init = function() {

    CBViewEditor.init.call(this);

    this.alternativeTextViewEditor      = CBViewEditor.editorForViewClassName("CBTextView");
    this.model.className                = "CBImageView";
    this.model.actualHeight             = null;
    this.model.actualWidth              = null;
    this.model.alternativeTextViewModel = this.alternativeTextViewEditor.model;
    this.model.displayHeight            = null;
    this.model.displayWidth             = null;
    this.model.filename                 = null;
    this.model.maxHeight                = null;
    this.model.maxWidth                 = null;
    this.model.URL                      = null;
    this.model.URLForHTML               = null;

    return this;
};

/**
 * @return instance type
 */
CBImageViewEditor.initWithModel = function(viewModel) {

    CBViewEditor.initWithModel.call(this, viewModel);

    var alternativeTextViewModel    = this.model.alternativeTextViewModel;
    this.alternativeTextViewEditor  = CBViewEditor.editorForViewModel(alternativeTextViewModel);

    return this;
};

/**
 * @return void
 */
CBImageViewEditor.createImageEditorElement = function() {

    var element         = document.createElement("div");
    element.className   = "CBImageViewImageEditor";
    var button          = document.createElement("button");
    button.textContent  = "Upload Image";
    var input           = document.createElement("input");
    input.type          = "file";
    input.style.display = "none";
    var listener        = input.click.bind(input);

    button.addEventListener("click", listener);

    listener            = this.uploadImage.bind(this);

    input.addEventListener("change", listener);

    element.appendChild(button);
    element.appendChild(input);

    this._imageEditorElement    = element;
    this._input                 = input;

    return this._imageEditorElement;
};

/**
 * @return Element
 */
CBImageViewEditor.element = function() {

    if (!this._element) {

        this._element           = document.createElement("div");
        this._element.className = "CBImageViewEditor";
        this._element.appendChild(this.imageEditorElement());
        this._element.appendChild(this.alternativeTextEditorElement());
    }

    return this._element;
};

/**
 * @return Element
 */
CBImageViewEditor.alternativeTextEditorElement = function() {

    this.alternativeTextViewEditor.labelText = "Alternative Text";
    return this.alternativeTextViewEditor.element();
};

/**
 * @return Element
 */
CBImageViewEditor.imageEditorElement = function() {

    if (!this._imageEditorElement)
    {
        this.createImageEditorElement();
        this.updateThumbnail();
    }

    return this._imageEditorElement;
};

/**
 * @return void
 */
CBImageViewEditor.thumbnailWasClicked = function() {

    if ("dark-background" == this._thumbnail.className) {

        this._thumbnail.className = "";

    } else {

        this._thumbnail.className = "dark-background";
    }
};

/**
 * @return void
 */
CBImageViewEditor.uploadImage = function() {

    if (this.xhr)
    {
        this.xhr.abort();
        this.xhr = null;
    }

    var formData = new FormData();
    formData.append("dataStoreID", CBPageEditor.model.dataStoreID);
    formData.append("image", this._input.files[0]);

    if (this.cropToHeight) {

        formData.append("cropToHeight", this.cropToHeight);
    }

    if (this.cropToWidth) {

        formData.append("cropToWidth", this.cropToWidth);
    }

    if (this.reduceToHeight) {

        formData.append("reduceToHeight", this.reduceToHeight);
    }

    if (this.reduceToWidth) {

        formData.append("reduceToWidth", this.reduceToWidth);
    }

    var xhr     = new XMLHttpRequest();
    xhr.onload  = this.uploadImageDidComplete.bind(this);
    xhr.open("POST", "/api/?className=CBAPIUploadImage");
    xhr.send(formData);

    this._xhr = xhr;
};

/**
 * @return void
 */
CBImageViewEditor.uploadImageDidComplete = function() {

    var response = Colby.responseFromXMLHttpRequest(this._xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
    else
    {
        this.model.actualHeight             = response.actualHeight;
        this.model.actualWidth              = response.actualWidth;
        this.model.filename                 = response.filename;
        this.model.URL                      = response.URL;
        this.model.URLForHTML               = response.URLForHTML;

        this.updateThumbnail();

        CBPageEditor.requestSave();
    }

    this._xhr = null;
};

/**
 * @return void
 */
CBImageViewEditor.updateThumbnail = function() {

    if (!this.model.URL)
    {
        return;
    }

    if (!this._thumbnail)
    {
        this._imageDimensions = document.createElement("div");
        this._imageEditorElement.insertBefore(this._imageDimensions, this._imageEditorElement.firstChild);
        this._thumbnail = document.createElement("img");
        this._imageEditorElement.insertBefore(this._thumbnail, this._imageEditorElement.firstChild);

        var listener = this.thumbnailWasClicked.bind(this);

        this._thumbnail.addEventListener("click", listener);
    }

    this._imageDimensions.textContent   = this.model.actualWidth + " × " + this.model.actualHeight;
    this._thumbnail.src                 = this.model.URL;
};

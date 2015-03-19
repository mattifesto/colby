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
CBImageViewEditor.initWithModel = function(model) {
    CBViewEditor.initWithModel.call(this, model);

    if (!model.alternativeTextViewModel) {
        model.alternativeTextViewModel = {"className":"CBTextView"};
    }

    this.alternativeTextViewEditor  = CBViewEditor.editorForViewModel(model.alternativeTextViewModel);

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
 * The primary purpose of the returned function is to toggle the background on
 * an image thumbnail so that a page editor can see an image with transparency
 * in different contexts. It can be called in response to some UI event, such
 * as a click on the image.
 *
 * @return function
 */
CBImageViewEditor.toggleBackground = function(element) {
    if ("dark-background" == element.className) {
        element.className = "";
    } else {
        element.className = "dark-background";
    }
};

/**
 * The primary purpose of the returned function is to update an element whose
 * purpose is to display the dimensions of an image. The function is generally
 * called in response to the image element sending a load event.
 *
 * @return function
 */
CBImageViewEditor.updateDimensions = function(imageElement, dimensionsElement) {
    dimensionsElement.textContent = imageElement.naturalWidth + " × " + imageElement.naturalHeight;
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
    xhr.open("POST", "/api/?class=CBImages&function=uploadAndReduceForAjax");
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
        var thumbnail   = document.createElement("img");
        var dimensions  = document.createElement("div");

        thumbnail.addEventListener("click",
            CBImageViewEditor.toggleBackground.bind(undefined, thumbnail));
        thumbnail.addEventListener("load",
            CBImageViewEditor.updateDimensions.bind(undefined, thumbnail, dimensions));

        this._imageEditorElement.insertBefore(dimensions, this._imageEditorElement.firstChild);
        this._imageEditorElement.insertBefore(thumbnail, this._imageEditorElement.firstChild);

        this._thumbnail = thumbnail;
    }

    this._thumbnail.src = this.model.URL;
};

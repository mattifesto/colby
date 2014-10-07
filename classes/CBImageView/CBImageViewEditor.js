"use strict";

var CBImageViewEditor           = Object.create(CBViewEditor);
CBImageViewEditor.chromeClass   = "CBTextViewEditorChrome";

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
    var button          = document.createElement("button");
    button.textContent  = "Upload Image";
    var input           = document.createElement("input");
    input.type          = "file";
    input.style.display = "none";
    var listener        = input.click.bind(input);

    button.addEventListener("click", listener);

    listener            = this.fileDidChange.bind(this);

    input.addEventListener("change", listener);

    element.appendChild(button);
    element.appendChild(input);

    this._imageEditorElement = element;

    return this._imageEditorElement;
};

/**
 * @return Element
 */
CBImageViewEditor.element = function() {

    if (!this._element) {

        this._element = document.createElement("div");
        this._element.appendChild(this.imageEditorElement());
        this._element.appendChild(this.alternativeTextEditorElement());
    }

    return this._element;
};

/**
 * @return Element
 */
CBImageViewEditor.alternativeTextEditorElement = function() {

    return this.alternativeTextViewEditor.element();
};

/**
 * @return Element
 */
CBImageViewEditor.imageEditorElement = function() {

    if (!this._imageEditorElement)
    {
        this.createImageEditorElement();
    }

    return this._imageEditorElement;
};

/**
 * @return void
 */
CBImageViewEditor.fileDidChange = function() {

};

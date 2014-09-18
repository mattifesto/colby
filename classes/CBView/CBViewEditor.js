"use strict";


var CBViewEditor =
{
    "chromeClass"       : "CBViewEditorStandardChrome",
    "deleteCallback"    : null
};

/**
 * This function creates a new view editor for an existing view model. It would
 * mostly be used when creating an editor for a model retrieved from within a
 * page model.
 *
 * @return instance type
 */
CBViewEditor.editorForViewModel = function(viewModel) {

    var editorClassName = viewModel.className + "Editor";
    var editor          = Object.create(window[editorClassName]);

    editor.initWithModel(viewModel);

    return editor;
};

/**
 * This function creates a new view editor containing a new view model for a
 * given view class name. It would mostly be used to create both a new view
 * and an editor to edit it when the user adds a view to their page.
 *
 * @return instance type
 */
CBViewEditor.editorForViewClassName = function(viewClassName) {

    var editorClassName = viewClassName + "Editor";
    var editor          = Object.create(window[editorClassName]);

    editor.init();

    return editor;
};

/**
 * This is a designated initializer.
 *
 * @return CBViewEditor
 */
CBViewEditor.init = function() {

    this.model =
    {
        className   : "CBView",
        ID          : Colby.random160(),
        version     : 1
    };

    return this;
};

/**
 * This is a designated initializer.
 *
 * @return CBViewEditor
 */
CBViewEditor.initWithModel = function(viewModel) {

    this.model = viewModel;

    return this;
};

/**
 * @return Element
 */
CBViewEditor.element = function() {

    if (!this._element)
    {
        this._element                   = document.createElement("div");
        this._element.style.margin      = "20px 0";
        this._element.style.textAlign   = "center";
        this._element.textContent       = "This view has no configurable properties.";
    }

    return this._element;
};

/**
 * This method will be renamed `element` eventually but to keep things running
 * it's starting off as `outerElement`.
 *
 * @return Element
 */
CBViewEditor.outerElement = function() {

    if (!this._outerElement) {

        this._outerElement = document.createElement("section");
        this._outerElement.classList.add("CBViewEditorChrome");
        this._outerElement.classList.add(this.chromeClass);
        this._outerElement.classList.add(this.model.className);

        var header      = document.createElement("header");
        var h1          = document.createElement("h1");
        h1.textContent  = this.model.className;

        header.appendChild(h1);

        if (this.deleteCallback) {

            var deleteButton            = document.createElement("button");
            deleteButton.textContent    = "Delete View";

            deleteButton.addEventListener(this.deleteCallback);

            header.appendChild(deleteButton);
        }

        this._outerElement.appendChild(header);
        this._outerElement.appendChild(this.element());
    }

    return this._outerElement;
};

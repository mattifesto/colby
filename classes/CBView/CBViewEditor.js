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

    var prototype   = this.editorPrototypeForViewClassName(viewModel.className);
    var editor      = Object.create(prototype);

    editor.initWithModel(viewModel);

    return editor;
};

/**
 * This function creates a new view editor containing a new view model for a
 * given view class name. It would mostly be used to create both a new view
 * and an editor to edit it when the user adds a view to their page.
 *
 * The model's `className` property is set by this method to allow simple views
 * with no properties to use this class as their view editor.
 *
 * @return instance type
 */
CBViewEditor.editorForViewClassName = function(viewClassName) {

    var prototype   = this.editorPrototypeForViewClassName(viewClassName);
    var editor      = Object.create(prototype);

    editor.init();

    editor.model.className = viewClassName;

    return editor;
};

/**
 * @return instance type
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
 * @return instance type
 */
CBViewEditor.initWithModel = function(viewModel) {

    this.model = viewModel;

    return this;
};

/**
 * If a prototype object exists for the standard editor class name, then it will
 * be returned. Otherwise this object will be returned. In that case, an
 * instance of a generic editor will be created which will preserve the model
 * but not allow any of its properties to be edited.
 *
 * @return string
 */
CBViewEditor.editorPrototypeForViewClassName = function(viewClassName) {

    var editorClassName = viewClassName + "Editor";
    var prototype       = window[editorClassName];

    if (prototype) {

        return prototype;

    } else {

        return this;
    }
};

/**
 * @return Element
 */
CBViewEditor.element = function() {

    if (!this._element) {

        this._element                   = document.createElement("div");
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

        this._outerElement          = document.createElement("section");
        this._outerElement.model    = this.model;
        var header                  = document.createElement("header");
        var h1                      = document.createElement("h1");
        h1.textContent              = this.model.className;

        this._outerElement.classList.add("CBViewEditorChrome");
        this._outerElement.classList.add(this.chromeClass);
        this._outerElement.classList.add(this.model.className);


        header.appendChild(h1);

        if (this.deleteCallback) {

            var deleteButton            = document.createElement("button");
            deleteButton.textContent    = "Delete View";

            deleteButton.addEventListener("click", this.deleteCallback);

            header.appendChild(deleteButton);
        }

        this._outerElement.appendChild(header);
        this._outerElement.appendChild(this.element());
    }

    return this._outerElement;
};

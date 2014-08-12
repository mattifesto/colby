"use strict";


var CBViewEditor = {};

/**
 * This is a designated initializer.
 *
 * @return CBViewEditor
 */
CBViewEditor.init = function()
{
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
CBViewEditor.initWithModel = function(viewModel)
{
    this.model = viewModel;

    return this;
};

/**
 * This function creates a new view editor containing a new view model for a
 * given view class name. It would mostly be used to create both a new view
 * and an editor to edit it when the user adds a view to their page.
 *
 * @return CBViewEditor
 */
CBViewEditor.createViewEditorWithClassName = function(viewClassName)
{
    var editorClassName = viewClassName + "Editor";
    var editor          = Object.create(window[editorClassName]).init();

    return editor;
};

/**
 * This function creates a new view editor for an existing view model. It would
 * mostly be used when creating an editor for a model retrieved from within a
 * page model.
 *
 * @return CBViewEditor
 */
CBViewEditor.createViewEditorWithModel = function(viewModel)
{
    var editorClassName = viewModel.className + "Editor";
    var editor          = Object.create(window[editorClassName]).initWithModel(viewModel);

    return editor;
};

/**
 * @return Element
 */
CBViewEditor.element = function()
{
    if (!this._element)
    {
        this._element                   = document.createElement("div");
        this._element.style.margin      = "20px 0";
        this._element.style.textAlign   = "center";
        this._element.textContent       = "This view has no configurable properties.";
    }

    return this._element;
};

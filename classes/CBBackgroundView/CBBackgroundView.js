"use strict";


function CBBackgroundViewEditor()
{
    this.model              = {};
    this.model.className    = "CBBackgroundView";
    this.model.ID           = Colby.random160();
    this.model.version      = 1;
}

/**
 * @return void
 */
CBBackgroundViewEditor.prototype.createElement = function()
{
    this._element           = document.createElement("div");
    this._element.className = "CBBackgroundViewEditor";

    this.createUploadBackgroundImageButton();
};

/**
 * @return void
 */
CBBackgroundViewEditor.prototype.createUploadBackgroundImageButton = function()
{
    var button          = document.createElement("button");
    button.textContent  = "Upload Background Image";

    this._element.appendChild(button);
};

/**
 * @return Element
 */
CBBackgroundViewEditor.prototype.element = function()
{
    if (!this._element)
    {
        this.createElement();
    }

    return this._element;
};

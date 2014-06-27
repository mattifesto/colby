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
    this.createOptionsElement();
};

/**
 * @return void
 */
CBBackgroundViewEditor.prototype.createOptionsElement = function()
{
    this._optionsElement            = document.createElement("div");
    this._optionsElement.className  = "options";

    this.createRepeatHorizontallyCheckbox();

    this._element.appendChild(this._optionsElement);
};

/**
 * @return void
 */
CBBackgroundViewEditor.prototype.createRepeatHorizontallyCheckbox = function()
{
    var checkbox    = document.createElement("input");
    checkbox.type   = "checkbox";

    this._optionsElement.appendChild(checkbox);
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

"use strict";


var CBBackgroundViewEditor = Object.create(CBViewEditor);

/**
 * @return CBBackgroundViewEditor
 */
CBBackgroundViewEditor.init = function()
{
    CBViewEditor.init.call(this);

    this.model.className = "CBBackgroundView";

    return this;
}

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
    var button          = document.createElement("button");
    button.textContent  = "Upload Background Image";

    this._element.appendChild(button);
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

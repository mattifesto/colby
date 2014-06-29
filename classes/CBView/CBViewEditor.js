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
CBViewEditor.initWithModel = function(model)
{
    this.model = model;

    return this;
};

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
}

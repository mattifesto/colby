"use strict";

/**
 * This control is used exclusively by the CBSectionListView object and it
 * it contains behavior and styling specific to that use.
 */
function CBSectionSelectionControl()
{
    this._element   = document.createElement("div");
    this._select    = document.createElement("select");
    this._button    = document.createElement("button");

    this._element.style.marginBottom    = "30px";
    this._element.style.marginTop       = "30px";
    this._element.style.textAlign       = "center";
    this._button.style.marginLeft       = "5px";

    this._element.appendChild(this._select);
    this._element.appendChild(this._button);
    this._button.appendChild(document.createTextNode("Insert"));

    for (var sectionTypeID in CBSectionDescriptors)
    {
        var option      = document.createElement("option");
        var description = document.createTextNode(CBSectionDescriptors[sectionTypeID].name);
        option.value    = sectionTypeID;

        option.appendChild(description);
        this._select.appendChild(option);
    }
}

/**
 * @return Element
 */
CBSectionSelectionControl.prototype.element = function()
{
    return this._element;
};

/**
 * @return void
 */
CBSectionSelectionControl.prototype.setAction = function(target, method)
{
    if (this._action)
    {
        this._button.removeEventListener('click', this._action, false);
    }

    var sender      = this;
    this._action    = function()
    {
        method.call(target, sender);
    };

    this._button.addEventListener('click', this._action, false);
};

/**
 * @return string
 */
CBSectionSelectionControl.prototype.value = function()
{
    return this._select.value;
};

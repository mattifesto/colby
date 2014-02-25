"use strict";


/**
 *
 */
function CBFileLinkControl(labelText)
{
    this._container = document.createElement("span");
    this._container.classList.add("CBFileLinkControl");

    this._input                 = document.createElement("input");
    this._input.type            = "file";
    this._input.style.display   = "none";
    this._container.appendChild(this._input);

    var anchor = document.createElement("a");
    anchor.style.cursor = "pointer";
    anchor.addEventListener('click', this.anchorClickHandler(), false);
    anchor.appendChild(document.createTextNode(labelText));
    this._container.appendChild(anchor);
}

/**
 * @return function
 */
CBFileLinkControl.prototype.anchorClickHandler = function()
{
    var input = this._input;

    var handler = function()
    {
        input.click();
    };

    return handler;
};

/**
 * @return Element
 */
CBFileLinkControl.prototype.rootElement = function()
{
    return this._container;
};

/**
 * @return void
 */
CBFileLinkControl.prototype.setAction = function(target, method)
{
    if (this._action)
    {
        this._input.removeEventListener('change', this._action, false);
    }

    var sender = this;

    this._action = function()
    {
        method.call(target, sender);
    };

    this._input.addEventListener('change', this._action, false);
};

/**
 * @return string
 */
CBFileLinkControl.prototype.files = function()
{
    return this._input.files;
};

"use strict";


/**
 * This control provides a single button to activate a hidden file input. It is
 * preferable to a visible file input in many cases where only the button is
 * wanted in the interface and not the display of the filename selected.
 */
function CBFileLinkControl(labelText)
{
    this._container = document.createElement("span");
    this._container.classList.add("CBFileLinkControl");

    this._input                 = document.createElement("input");
    this._input.type            = "file";
    this._input.style.display   = "none";
    this._container.appendChild(this._input);

    var button = document.createElement("button");
    button.addEventListener('click', this.clickCallback(), false);
    button.appendChild(document.createTextNode(labelText));
    this._container.appendChild(button);
}

/**
 * @return function
 */
CBFileLinkControl.prototype.clickCallback = function()
{
    var self        = this;
    var callback    = function()
    {
        self._input.click();
    };

    return callback;
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

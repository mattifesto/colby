"use strict";


/**
 *
 */
function CBCheckboxControl(labelText)
{
    var checkboxID = "checkbox-" + Colby.random160();

    this._container = document.createElement("span");
    this._container.classList.add("CBCheckboxControl");

    this._input         = document.createElement("input");
    this._input.type    = "checkbox";
    this._input.id      = checkboxID;
    this._container.appendChild(this._input);

    var label               = document.createElement("label");
    label.for               = checkboxID;
    label.style.marginLeft  = "0.5em";
    label.appendChild(document.createTextNode(labelText));
    this._container.appendChild(label);
}

/**
 * @return Element
 */
CBCheckboxControl.prototype.rootElement = function()
{
    return this._container;
};

/**
 * @return void
 */
CBCheckboxControl.prototype.setAction = function(target, method)
{
    if (this.action)
    {
        this._input.removeEventListener('change', this.action, false);
    }

    var sender = this;

    this.action = function()
    {
        method.call(target, sender);
    };

    this._input.addEventListener('change', this.action, false);
};

/**
 * @return string
 */
CBCheckboxControl.prototype.isChecked = function()
{
    return this._input.checked;
};

/**
 * @return void
 */
CBCheckboxControl.prototype.setIsChecked = function(isChecked)
{
    this._input.checked = isChecked;
};

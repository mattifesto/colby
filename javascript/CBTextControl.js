"use strict";


/**
 *
 */
function CBTextControl(labelText)
{
    this.input = document.createElement("input");
    this.input.type = "text";

    this.label = document.createElement("label");
    this.label.classList.add("CBTextControl");

    var textNode = document.createTextNode(labelText);

    this.label.appendChild(textNode);
    this.label.appendChild(this.input);
}

/**
 * @return Element
 */
CBTextControl.prototype.rootElement = function()
{
    return this.label;
};

/**
 * @return void
 */
CBTextControl.prototype.setAction = function(target, method)
{
    if (this.action)
    {
        this.input.removeEventListener('input', this.action, false);
    }

    var sender = this;

    this.action = function()
    {
        method.call(target, sender);
    };

    this.input.addEventListener('input', this.action, false);
};

/**
 * @return string
 */
CBTextControl.prototype.value = function()
{
    return this.input.value;
};

/**
 * @return void
 */
CBTextControl.prototype.setValue = function(value)
{
    this.input.value = value;
};

"use strict";


/**
 *
 */
function CBTextAreaControl(labelText)
{
    this._textArea = document.createElement("textarea");

    this._label = document.createElement("label");
    this._label.classList.add("CBTextAreaControl");

    var textNode = document.createTextNode(labelText);

    this._label.appendChild(textNode);
    this._label.appendChild(this._textArea);
}

/**
 * @return Element
 */
CBTextAreaControl.prototype.rootElement = function()
{
    return this._label;
};

/**
 * @return void
 */
CBTextAreaControl.prototype.setAction = function(target, method)
{
    if (this.action)
    {
        this._textArea.removeEventListener('input', this.action, false);
    }

    var sender = this;

    this.action = function()
    {
        method.call(target, sender);
    };

    this._textArea.addEventListener('input', this.action, false);
};

/**
 * @return string
 */
CBTextAreaControl.prototype.value = function()
{
    return this._textArea.value;
};

/**
 * @return void
 */
CBTextAreaControl.prototype.setValue = function(value)
{
    this._textArea.value = value;
};

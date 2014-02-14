"use strict";


/**
 *
 */
function CBSelectionControl(labelText)
{
    this.select = document.createElement("select");

    this.label = document.createElement("label");
    this.label.classList.add("CBSelectionControl");

    var textNode = document.createTextNode(labelText);

    this.label.appendChild(textNode);
    this.label.appendChild(this.select);
}

/**
 * @return Element
 */
CBSelectionControl.prototype.rootElement = function()
{
    return this.label;
};

/**
 * @return void
 */
CBSelectionControl.prototype.setAction = function(target, method)
{
    if (this.action)
    {
        this.select.removeEventListener('change', this.action, false);
    }

    var sender = this;

    this.sendAction = function()
    {
        method.call(target, sender);
    };

    this.select.addEventListener('change', this.sendAction, false);
};

/**
 * @return void
 */
CBSelectionControl.prototype.appendOption = function(value, description)
{
    var option = document.createElement("option");
    option.value    = value;

    var text = document.createTextNode(description);

    option.appendChild(text);
    this.select.appendChild(option);
};

/**
 * @return string
 */
CBSelectionControl.prototype.value = function()
{
    return this.select.value;
};

/**
 * @return void
 */
CBSelectionControl.prototype.setValue = function(value)
{
    this.select.value = value;
};

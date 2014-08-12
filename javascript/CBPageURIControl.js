"use strict";


/**
 *
 */
function CBPageURIControl()
{
    this._isDisabled = false;

    /**
     *
     */

    var textFieldID = Colby.random160();


    /**
     *
     */

    var container = document.createElement("div");
    container.classList.add("CBPageURIControl");
    this.container = container;


    /**
     *
     */

    var labelForTextField       = document.createElement("label");
    labelForTextField.htmlFor   = textFieldID;
    labelForTextField.appendChild(document.createTextNode("URI"));

    this.container.appendChild(labelForTextField);


    /**
     *
     */

    var labelForCheckbox    = document.createElement("label");
    var checkbox            = document.createElement("input");
    checkbox.type           = "checkbox";
    labelForCheckbox.appendChild(checkbox);
    labelForCheckbox.appendChild(document.createTextNode("Static"));
    this.checkbox           = checkbox;

    this.container.appendChild(labelForCheckbox);


    /**
     *
     */

    var textField       = document.createElement("input");
    textField.disabled  = true;
    textField.id        = textFieldID;
    textField.type      = "text";
    this.textField      = textField;

    this.container.appendChild(textField);

    /**
     *
     */

    this.initializeEventListeners();
}


/**
 * The logic here is not entirely clear. We can add the action directly to the
 * text field because the only time the action will be sent is when the text
 * field is edited and this will only be allowed to happen when the static
 * checkbox is checked and the control isn't in read only mode (which is set
 * by the owner.)
 *
 * @return void
 */
CBPageURIControl.prototype.setAction = function(target, method)
{
    if (this.sendAction)
    {
        this.textField.removeEventListener('input', this.sendAction, false);
    }

    var sender = this;

    this.sendAction = function()
    {
        method.call(target, sender);
    };

    this.textField.addEventListener('input', this.sendAction, false);
};


/**
 * @return void
 */
CBPageURIControl.prototype.initializeEventListeners = function()
{
    var self = this;

    var checkboxChanged = function()
    {
        self.updateState();
        self.sendAction();
    };

    this.checkbox.addEventListener('change', checkboxChanged, false);
};

/**
 * @return boolean
 */
CBPageURIControl.prototype.isDisabled = function()
{
    return this._isDisabled;
};

/**
 * @return void
 */
CBPageURIControl.prototype.setIsDisabled = function(isDisabled)
{
    this._isDisabled = isDisabled;

    this.updateState();
};

/**
 * @return boolean
 */
CBPageURIControl.prototype.isStatic = function()
{
    return this.checkbox.checked;
};

/**
 * @return void
 */
CBPageURIControl.prototype.setIsStatic = function(isStatic)
{
    this.checkbox.checked = !!isStatic;

    this.updateState();
};


/**
 * @return Element
 */
CBPageURIControl.prototype.rootElement = function()
{
    return this.container;
};


/**
 * If the owner of this control sets and action this method will be replaced
 * with one that does something.
 *
 * @return void
 */
CBPageURIControl.prototype.sendAction = function()
{
};

/**
 * @return void
 */
CBPageURIControl.prototype.updateState = function()
{
    if (this.isDisabled())
    {
        this.textField.disabled = true;
        this.checkbox.disabled  = true;
    }
    else
    {
        this.checkbox.disabled  = false;

        if (this.isStatic())
        {
            this.textField.disabled = false;
        }
        else
        {
            this.textField.disabled = true;
        }
    }
};


/**
 * @return string
 */
CBPageURIControl.prototype.URI = function()
{
    return this.textField.value;
};

/**
 * @return void
 */
CBPageURIControl.prototype.setURI = function(value)
{
        this.textField.value = value;
};

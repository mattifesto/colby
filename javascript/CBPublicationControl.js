"use strict";


/**
 *
 */
function CBPublicationControl()
{
    /**
     *
     */

    var container = document.createElement("div");
    container.classList.add("CBPublicationControl");
    this._container = container;


    /**
     *
     */

    var labelForCheckbox    = document.createElement("label");
    var checkbox            = document.createElement("input");
    checkbox.type           = "checkbox";
    labelForCheckbox.appendChild(checkbox);
    labelForCheckbox.appendChild(document.createTextNode("Published"));
    this._checkbox           = checkbox;

    container.appendChild(labelForCheckbox);


    /**
     *
     */

    var textField   = document.createElement("input");
    textField.type  = "text";
    this._textField  = textField;

    container.appendChild(textField);

    /**
     *
     */

    this.initializeEventListeners();
}


/**
 * @return void
 */
CBPublicationControl.prototype.setAction = function(target, method)
{
    var sender = this;

    this.sendAction = function()
    {
        method.call(target, sender);
    };
};


/**
 * @return void
 */
CBPublicationControl.prototype.initializeEventListeners = function()
{
    var self = this;

    var textFieldChanged = function()
    {
        self.textFieldChanged();
    };

    this._textField.addEventListener('change', textFieldChanged, false);

    var isPublishedChanged = function()
    {
        self.isPublishedChanged();
        self.sendAction();
    };

    this._checkbox.addEventListener('change', isPublishedChanged, false);
};

/**
 * @return boolean
 */
CBPublicationControl.prototype.isPublished = function()
{
    return this._checkbox.checked;
};

/**
 * @return void
 */
CBPublicationControl.prototype.setIsPublished = function(isPublished)
{
    this._checkbox.checked = !!isPublished;
    this.isPublishedChanged();

};

/**
 * @return void
 */
CBPublicationControl.prototype.isPublishedChanged = function()
{
    if (this._checkbox.checked && this._textField.value == "")
    {
        this.setPublicationTimeStamp(Date.now() / 1000);
    }

    this._textField.disabled = this._checkbox.checked;
};

/**
 * @return string
 */
CBPublicationControl.prototype.publicationTimeStamp = function()
{
    return this._publicationTimeStamp;
};

/**
 * @return void
 */
CBPublicationControl.prototype.setPublicationTimeStamp = function(timeStamp)
{
    timeStamp                   = parseInt(timeStamp, 10);
    timeStamp                   = isNaN(timeStamp) ? null : timeStamp;
    this._publicationTimeStamp  = timeStamp;

    if (timeStamp)
    {
        var date = new Date(timeStamp * 1000);

        this._textField.value = date.toLocaleString();
    }
    else
    {
        this._textField.value = "";
    }
};

/**
 * @return Element
 */
CBPublicationControl.prototype.rootElement = function()
{
    return this._container;
};


/**
 * If the owner of this control sets and action this method will be replaced
 * with one that does something.
 *
 * @return void
 */
CBPublicationControl.prototype.sendAction = function()
{
};

/**
 * @return void
 */
CBPublicationControl.prototype.textFieldChanged = function() {
    var value = this._textField.value;

    if (value.match(/^\s*$/)) {
        this.setPublicationTimeStamp(null);
        this.sendAction();
    } else {
        var date = new Date(value);

        if (isNaN(date)) {
            this._textField.style.backgroundColor = "hsl(0, 100%, 90%)";
        } else {
            this._textField.style.backgroundColor = "";
            this.setPublicationTimeStamp(date.getTime() / 1000);
            this.sendAction();
        }
    }
};

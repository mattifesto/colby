"use strict";

var CBTextViewEditor            = Object.create(CBViewEditor);
CBTextViewEditor.chromeClass    = "CBTextViewEditorChrome";
CBTextViewEditor.labelText      = "Text";

/**
 * @return instance type
 */
CBTextViewEditor.init = function() {

    CBViewEditor.init.call(this);

    this.model.className    = "CBTextView";
    this.model.text         = "";
    this.model.HTML         = "";

    return this;
};

/**
 * @return void
 */
CBTextViewEditor.createElement = function() {

    this._element           = document.createElement("div");
    this._element.className = this.model.className;
    this._input             = document.createElement("input");
    this._input.id          = Colby.random160();
    this._input.type        = "text";
    this._input.value       = this.model.text;
    var label               = document.createElement("label");
    label.htmlFor           = this._input.id;
    label.textContent       = this.labelText;

    this._element.appendChild(label);
    this._element.appendChild(this._input);

    var listener = this.textDidChange.bind(this);

    this._input.addEventListener("input", listener);
};

/**
 * @return Element
 */
CBTextViewEditor.element = function() {

    if (!this._element)
    {
        this.createElement();
    }

    return this._element;
};

/**
 * @return void
 */
CBTextViewEditor.textDidChange = function() {

    var text        = this._input.value;
    this.model.text = text;
    this.model.HTML = Colby.textToHTML(text);

    CBPageEditor.requestSave();
};

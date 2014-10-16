"use strict";

var CBTextViewEditor = Object.create(CBViewEditor);

Colby.extend(CBTextViewEditor, {

    chromeClass                         : "CBTextViewEditorChrome",
    editorIsMultiLine                   : false,
    labelText                           : "Text",

    /**
     * @return void
     */
    setText : function(text) {

        this.model.text = text;
        this.model.HTML = this.textToHTML(text);

        CBPageEditor.requestSave();
    },

    /**
     * Subclasses may override this function to provide custom text to HTML
     * conversion for simple markup and markdown scenarios.
     *
     * @return string
     */
    textToHTML : function (text) {

        return Colby.textToHTML(text);
    }
});


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
    this._element.className = "CBTextViewEditor";

    if (this.editorIsMultiLine) {

        this._input         = document.createElement("textarea");

    } else {

        this._input         = document.createElement("input");
        this._input.type    = "text";
    }

    this._input.id          = Colby.random160();
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

    this.setText(this._input.value);
};

"use strict";

var CBTextViewEditor                    = Object.create(CBViewEditor);
CBTextViewEditor.contentTypeSingleLine  = 0;
CBTextViewEditor.contentTypeMultiLine   = 1;
CBTextViewEditor.contentTypeMarkaround  = 2;
CBTextViewEditor.chromeClass            = "CBTextViewEditorChrome";
CBTextViewEditor.labelText              = "Text";
CBTextViewEditor.editorIsMultiLine      = false;
CBTextViewEditor.contentType            = CBTextViewEditor.contentTypeSingleLine;

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

    var text        = this._input.value;
    this.model.text = text;

    switch (this.contentType) {

        case CBTextViewEditor.contentTypeSingleLine:

            this.model.HTML = Colby.textToHTML(text);
            break;

        case CBTextViewEditor.contentTypeMultiLine:

            /**
             * TODO: Add multi-line non-markaround parsing.
             */

            this.model.HTML = CBMarkaround.parse(text);
            break;

        case CBTextViewEditor.contentTypeMarkaround:

            this.model.HTML = CBMarkaround.parse(text);
            break;

        default:

            throw "Unhandled case in switch statement.";
    }

    CBPageEditor.requestSave();
};

Colby.extend(CBTextViewEditor, {

    /**
     * @return void
     */
    setText : function(text) {

        this.model.text = text;
        this.model.HTML = Colby.textToHTML(text);

        CBPageEditor.requestSave();
    }
});

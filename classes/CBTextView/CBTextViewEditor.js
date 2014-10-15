"use strict";

var CBTextViewEditor = Object.create(CBViewEditor);

Colby.extend(CBTextViewEditor, {

    chromeClass                         : "CBTextViewEditorChrome",
    contentType                         : 0,
    contentTypeSingleLinePlainText      : 0,
    contentTypeSingleLineFormattedText  : 1,
    contentTypeMultiLinePlainText       : 2,
    contentTypeMultiLineFormattedText   : 3,
    contentTypeMultiLineMarkaround      : 4,
    editorIsMultiLine                   : false,
    labelText                           : "Text",

    setContentType : function(contentType) {

        this.model.contentType = parseInt(contentType)
    }
});


/**
 * @return instance type
 */
CBTextViewEditor.init = function() {

    CBViewEditor.init.call(this);

    this.model.className    = "CBTextView";
    this.model.contentType  = this.contentTypeSingleLinePlainText;
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

    /**
     * 2014.10.14 TODO:
     *  Most of these content types are not supported correctly and need to
     *  be implemented.
     */
    switch (this.model.contentType) {

        case this.contentTypeSingleLinePlainText:

            this.model.HTML = Colby.textToHTML(text);
            break;

        case this.contentTypeSingleLineFormattedText:

            this.model.HTML = Colby.textToHTML(text);
            break;

        case this.contentTypeMultiLinePlainText:

            this.model.HTML = CBMarkaround.parse(text);
            break;

        case this.contentTypeMultiLineFormattedText:

            this.model.HTML = CBMarkaround.parse(text);
            break;

        case this.contentTypeMultiLineMarkaround:

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

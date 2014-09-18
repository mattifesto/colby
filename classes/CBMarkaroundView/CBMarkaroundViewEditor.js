"use strict";


var CBMarkaroundViewEditor          = Object.create(CBViewEditor);
CBMarkaroundViewEditor.chromeClass  = "CBMarkaroundViewEditorChrome";
CBMarkaroundViewEditor.labelText    = "Markaround";

/**
 * @return CBMarkaroundViewEditor
 */
CBMarkaroundViewEditor.init = function() {

    CBViewEditor.init.call(this);

    this.model.className    = "CBMarkaroundView";
    this.model.markaround   = '';
    this.model.HTML         = '';

    return this;
};

/**
 * @return void
 */
CBMarkaroundViewEditor.createElement = function() {

    this._element           = document.createElement("div");
    this._element.className = "CBMarkaroundViewEditor";
    this._textarea          = document.createElement("textarea");
    this._textarea.id       = Colby.random160();
    this._textarea.value    = this.model.markaround;
    var label               = document.createElement("label");
    label.htmlFor           = this._textarea.id;
    label.textContent       = this.labelText;

    var listener = this.markaroundDidChange.bind(this);

    this._textarea.addEventListener("input", listener);

    this._element.appendChild(label);
    this._element.appendChild(this._textarea);
};

/**
 * @return Element
 */
CBMarkaroundViewEditor.element = function() {

    if (!this._element)
    {
        this.createElement();
    }

    return this._element;
};

/**
 * TODO
 *  This method should restart a CBDelayTimer which will send the markaround
 *  to the server to be parsed. This will accomplish two things:
 *
 *      1. The markaround will be processed asynchonously and not slow down
 *         the user interface.
 *      2. The markaround won't be process for every single keystroke.
 *
 * @return void
 */
CBMarkaroundViewEditor.markaroundDidChange = function() {

    this.model.markaround   = this._textarea.value;
    this.model.HTML         = CBMarkaround.parse(this.model.markaround);

    CBPageEditor.requestSave();
};

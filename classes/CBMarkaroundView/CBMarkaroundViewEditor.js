"use strict";


var CBMarkaroundViewEditor          = Object.create(CBViewEditor);
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
    var label               = document.createElement("label");
    label.htmlFor           = this._textarea.id;
    label.textContent       = this.labelText;

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

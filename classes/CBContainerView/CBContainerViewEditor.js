"use strict";

var CBContainerViewEditor = Object.create(CBViewEditor);

/**
 * @return CBContainerViewEditor
 */
CBContainerViewEditor.init = function() {

    CBViewEditor.init.call(this);

    this.model.className        = "CBContainerView";
    this.model.subviewModels    = [];

    return this;
};

/**
 * @return void
 */
CBContainerViewEditor.createChildViewsElement = function() {

    var childViewsElement               = document.createElement("div");
    childViewsElement.className         = "children";
    var childViewsTitleElement          = document.createElement("h1");
    childViewsTitleElement.textContent  = "Subviews";
    var childListView                   = new CBSectionListView(this.model.subviewModels);

    childViewsElement.appendChild(childViewsTitleElement);
    childViewsElement.appendChild(childListView.element());
    this._element.appendChild(childViewsElement);
};

/**
 * @return void
 */
CBContainerViewEditor.createElement = function() {

    this._element           = document.createElement("div");
    this._element.className = "CBContainerViewEditor";

    this.createChildViewsElement();
};

/**
 * @return Element
 */
CBContainerViewEditor.element = function() {

    if (!this._element)
    {
        this.createElement();
    }

    return this._element;
};

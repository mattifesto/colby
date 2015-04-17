"use strict";

var CBContainerViewEditor = Object.create(CBViewEditor);

Colby.extend(CBContainerViewEditor, {

    editorElementClasses : ["CBContainerViewEditor"]
});

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
    childViewsElement.className         = "CBContainerViewEditorSubviews";

    childViewsElement.appendChild(CBModelArrayEditor.createEditor({
        handleSpecChanged   : CBPageEditor.requestSave.bind(CBPageEditor),
        specArray           : this.model.subviewModels }));

    this._element.appendChild(childViewsElement);
};

/**
 * @return void
 */
CBContainerViewEditor.createElement = function() {

    var element     = document.createElement("div");
    this._element   = element;

    this.editorElementClasses.forEach(function(elementClass) { element.classList.add(elementClass) });

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

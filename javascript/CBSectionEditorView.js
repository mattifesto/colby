"use strict";

/**
 *
 */
function CBSectionEditorView(model, sectionListView)
{
    var title               = CBSectionDescriptors[model.sectionTypeID].name;

    this._element           = document.createElement("section");
    this._element.id        = "s" + model.sectionID;
    this._element.classList.add("CBSectionEditorView");

    var header              = document.createElement("header");
    header.style.overflow   = "hidden";
    header.appendChild(document.createTextNode(title));
    this._element.appendChild(header);


    var deleteButton            = document.createElement("button");
    var deleteSectionCallback   = sectionListView.deleteSectionCallback(model);
    deleteButton.style.float    = "right";
    deleteButton.addEventListener('click', deleteSectionCallback, false);
    deleteButton.appendChild(document.createTextNode("Delete"));
    header.appendChild(deleteButton);


    this._innerElement  = document.createElement("div");
    this._element.appendChild(this._innerElement);

    var sectionEditorConstructor = CBPageEditor.sectionEditors[model.sectionTypeID];

    // TODO: this is an odd model, the editor creates itself and then places itself in the tree. I'm not sure about this.

    new sectionEditorConstructor(CBPageEditor.model, model, this._innerElement);
}

/**
 * @return void
 */
CBSectionEditorView.prototype.element = function()
{
    return this._element;
}

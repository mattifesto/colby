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
    header.appendChild(document.createTextNode(title));
    this._element.appendChild(header);


    var deleteButton            = document.createElement("button");
    var deleteSectionCallback   = sectionListView.deleteSectionCallback(model);
    deleteButton.addEventListener('click', deleteSectionCallback, false);
    deleteButton.appendChild(document.createTextNode("Delete Section"));
    header.appendChild(deleteButton);


    this._innerElement  = document.createElement("div");
    this._element.appendChild(this._innerElement);

    var sectionEditorConstructor    = CBPageEditor.sectionEditors[model.sectionTypeID];
    var sectionEditor               = new sectionEditorConstructor(CBPageEditor.model, model);
    this._innerElement.appendChild(sectionEditor.element());
}

/**
 * @return void
 */
CBSectionEditorView.prototype.element = function()
{
    return this._element;
}

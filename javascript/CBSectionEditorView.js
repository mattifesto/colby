"use strict";

/**
 *
 */
function CBSectionEditorView(model, sectionListView)
{
    var sectionEditor;
    var title;

    if (CBSectionDescriptors[model.sectionTypeID])
    {
        title = CBSectionDescriptors[model.sectionTypeID].name;
    }
    else
    {
        title = 'This is a section for which a descriptor has not been included.';
    }

    this._element           = document.createElement("section");
    this._element.id        = "s" + model.sectionID;
    this._element.classList.add("CBSectionEditorView");

    var header              = document.createElement("header");
    header.appendChild(document.createTextNode(title));
    this._element.appendChild(header);


    var deleteButton            = document.createElement("button");
    var deleteSectionCallback   = sectionListView.deleteSection.bind(sectionListView, model);
    deleteButton.addEventListener('click', deleteSectionCallback, false);
    deleteButton.appendChild(document.createTextNode("Delete Section"));
    header.appendChild(deleteButton);


    this._innerElement  = document.createElement("div");
    this._element.appendChild(this._innerElement);

    var sectionEditorConstructor = CBPageEditor.sectionEditors[model.sectionTypeID];

    if (sectionEditorConstructor)
    {
        sectionEditor = new sectionEditorConstructor(CBPageEditor.model, model);

        this._innerElement.appendChild(sectionEditor.element());
    }
    else
    {
        sectionEditor                   = document.createElement("div");
        sectionEditor.textContent       = "This section has no properties";
        sectionEditor.style.marginTop   = "10px";
        sectionEditor.style.textAlign   = "center";

        this._innerElement.appendChild(sectionEditor);
    }
}

/**
 * @return void
 */
CBSectionEditorView.prototype.element = function()
{
    return this._element;
};

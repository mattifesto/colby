"use strict";

/**
 *
 */
function CBSectionEditorView(model)
{
    if (!model.sectionID)
    {
        model.sectionID = Colby.random160();
    }

    var title               = CBSectionDescriptors[model.sectionTypeID].name;

    this._element           = document.createElement("section");
    this._element.id        = "s" + model.sectionID;
    this._element.classList.add("CBSectionEditorView");

    var header              = document.createElement("header");
    header.appendChild(document.createTextNode(title));
    this._element.appendChild(header);


    var selectionControl    = new CBSelectionControl(" insert ");
    this._selectionControl  = selectionControl;
    header.appendChild(selectionControl.rootElement());

    for (var ID in CBSectionDescriptors)
    {
        selectionControl.appendOption(ID, CBSectionDescriptors[ID].name);
    }


    var button  = document.createElement("button");
    button.appendChild(document.createTextNode("Insert"));
    header.appendChild(button);

    var insertSection = function()
    {
        var newSectionTypeID = selectionControl.value();

        CBPageEditor.insertNewSectionBefore(newSectionTypeID, sectionID);
    }

    button.addEventListener('click', insertSection, false);


    var button  = document.createElement("button");
    button.appendChild(document.createTextNode("Delete"));
    header.appendChild(button);

    var removeSection = function()
    {
        CBPageEditor.removeSection(this._element.id);
    }

    button.addEventListener('click', removeSection, false);


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

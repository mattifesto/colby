"use strict";

/**
 *
 */
function CBSection(sectionID, title)
{
    var sectionElementID    = "s" + sectionID;
    var outerElement        = document.createElement("section");
    outerElement.id         = sectionElementID;
    this._outerElement  = outerElement;


    var header      = document.createElement("header");
    header.appendChild(document.createTextNode(title));
    this._header    = header;
    outerElement.appendChild(header);


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
        CBPageEditor.removeSection(sectionID);
    }

    button.addEventListener('click', removeSection, false);


    var innerElement    = document.createElement("div");
    this._innerElement  = innerElement;
    outerElement.appendChild(innerElement);
}

/**
 * @return void
 */
CBSection.prototype.innerElement = function()
{
    return this._innerElement;
}

/**
 * @return void
 */
CBSection.prototype.outerElement = function()
{
    return this._outerElement;
}

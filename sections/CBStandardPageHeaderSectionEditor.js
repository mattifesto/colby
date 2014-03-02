"use strict";

/**
 *
 */
function CBStandardPageHeaderSectionEditor(pageModel, sectionModel)
{
    this._element                   = document.createElement("div");
    this._element.style.textAlign   = "center";
    this._element.classList.add("container");
    this._element.appendChild(document.createTextNode("This section has no configurable properties"));
}

/**
 * @return void
 */
CBStandardPageHeaderSectionEditor.prototype.element = function()
{
    return this._element;
};

/**
 * @return void
 */
CBStandardPageHeaderSectionEditor.register = function()
{
    CBPageEditor.registerSectionEditor(CBStandardPageHeaderSectionTypeID, CBStandardPageHeaderSectionEditor);
}

document.addEventListener("CBPageEditorDidLoad", CBStandardPageHeaderSectionEditor.register, false);

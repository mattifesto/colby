"use strict";

/**
 *
 */
function CBStandardPageFooterSectionEditor(pageModel, sectionModel)
{
    this._element                   = document.createElement("div");
    this._element.style.textAlign   = "center";
    this._element.classList.add("container");
    this._element.appendChild(document.createTextNode("This section has no configurable properties"));
}

/**
 * @return void
 */
CBStandardPageFooterSectionEditor.prototype.element = function()
{
    return this._element;
};

/**
 * @return void
 */
CBStandardPageFooterSectionEditor.register = function()
{
    CBPageEditor.registerSectionEditor(CBStandardPageFooterSectionTypeID, CBStandardPageFooterSectionEditor);
};

document.addEventListener("CBPageEditorDidLoad", CBStandardPageFooterSectionEditor.register, false);

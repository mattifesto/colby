"using strict";

/**
 *
 */
function CBStandardPageHeaderSectionEditor(pageModel, sectionModel, sectionElement)
{
    this.pageModel      = pageModel;
    this.sectionModel   = sectionModel;
    this.sectionElement = sectionElement;

    var div     = document.createElement("div");
    div.classList.add("container");
    var text    = document.createTextNode("This section has no configurable properties");
    div.appendChild(text);

    this.sectionElement.appendChild(div);
}

/**
 * @return void
 */
CBStandardPageHeaderSectionEditor.register = function()
{
    CBPageEditor.registerSectionEditor(CBStandardPageHeaderSectionTypeID, CBStandardPageHeaderSectionEditor);
}

document.addEventListener("CBPageEditorDidLoad", CBStandardPageHeaderSectionEditor.register, false);

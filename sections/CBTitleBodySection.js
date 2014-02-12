"using strict";

/**
 *
 */
function CBTitleSection(pageModel, sectionModel, sectionElement)
{
    this.pageModel      = pageModel;
    this.sectionModel   = sectionModel;
    this.sectionElement = sectionElement;

    var titleControl = new CBTextControl("Title");

    titleControl.setValue(this.sectionModel.title);
    titleControl.setAction(this, this.translateTitle);

    this.sectionElement.appendChild(titleControl.rootElement());
}

CBTitleSection.ID = "CBTitleSection";

/**
 * @return Object
 */
CBTitleSection.newModel = function()
{
    var model =
    {
        schema:         "CBTitleSection",
        schemaVersion:  1,
        title:          "",
        titleHTML:      ""
    };

    return model;
};

/**
 * @return void
 */
CBTitleSection.prototype.translateTitle = function(value)
{
    this.sectionModel.title = value.trim();
    this.sectionModel.titleHTML = Colby.textToHTML(this.sectionModel.title);
    
    CBPageEditor.requestSave();
};

/**
 * TODO: Walk through whether these need to be registered still. Can the names
 *       of the sections just be registered in a more simple manner?
 */
CBTitleSection.register = function()
{
    CBPageEditor.registerBodySection(CBTitleSection);
}

document.addEventListener("CBPageEditorDidLoad", CBTitleSection.register, false);

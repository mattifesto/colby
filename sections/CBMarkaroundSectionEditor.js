"use strict";


/**
 *
 */
function CBMarkaroundSectionEditor(pageModel, sectionModel, sectionElement)
{
    this._pageModel      = pageModel;
    this._sectionModel   = sectionModel;
    this._sectionElement = sectionElement;

    var heading1Control = new CBTextControl("Heading 1");
    heading1Control.setValue(this._sectionModel.heading1);
    heading1Control.setAction(this, this.translateHeading1);
    heading1Control.rootElement().classList.add("standard");
    this._sectionElement.appendChild(heading1Control.rootElement());

    var heading2Control = new CBTextControl("Heading 2");
    heading2Control.setValue(this._sectionModel.heading2);
    heading2Control.setAction(this, this.translateHeading2);
    heading2Control.rootElement().classList.add("standard");
    this._sectionElement.appendChild(heading2Control.rootElement());

    var contentControl = new CBTextAreaControl("Content");
    contentControl.setValue(this._sectionModel.contentMarkaround);
    contentControl.setAction(this, this.translateContent);
    contentControl.rootElement().classList.add("standard");
    this._sectionElement.appendChild(contentControl.rootElement());
}

/**
 * @return void
 */
CBMarkaroundSectionEditor.register = function()
{
    CBPageEditor.registerSectionEditor(CBMarkaroundSectionTypeID, CBMarkaroundSectionEditor);
};

/**
 * @return void
 */
CBMarkaroundSectionEditor.prototype.translateContent = function(sender)
{
    if (!this._request)
    {
        this._request       = new CBContinuousAjaxRequest("/admin/pages/api/translate-markaround/");
        this._request.delay = 1000;

        var self = this;

        var handler = function(xhr)
        {
            self.translateContentDidComplete(xhr);
        }

        this._request.onload = handler;
    }

    var formData = new FormData();
    formData.append("contentMarkaround", sender.value());

    this._request.makeRequestWithFormData(formData);
};

/**
 * @return void
 */
CBMarkaroundSectionEditor.prototype.translateContentDidComplete = function(xhr)
{
    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
    else
    {
        this._sectionModel.contentMarkaround    = response.contentMarkaround;
        this._sectionModel.contentHTML          = response.contentHTML;

        CBPageEditor.requestSave();
    }
};

/**
 * @return void
 */
CBMarkaroundSectionEditor.prototype.translateHeading1 = function(sender)
{
    this._sectionModel.heading1 = sender.value().trim();
    this._sectionModel.heading1HTML = Colby.textToHTML(this._sectionModel.heading1);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBMarkaroundSectionEditor.prototype.translateHeading2 = function(sender)
{
    this._sectionModel.heading2 = sender.value().trim();
    this._sectionModel.heading2HTML = Colby.textToHTML(this._sectionModel.heading2);

    CBPageEditor.requestSave();
};

document.addEventListener("CBPageEditorDidLoad", CBMarkaroundSectionEditor.register, false);

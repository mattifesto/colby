"use strict";


function CBBackgroundView()
{
    this.model              = {};
    this.model.className    = "CBBackgroundView";
    this.model.ID           = Colby.random160();
    this.model.version      = 1;
}

CBBackgroundView.prototype.editorElement = function()
{
    var element             = document.createElement("div");
    element.style.margin    = "10px 0px";
    element.style.textAlign = "center";
    element.textContent     = "This is placeholder text for the editor.";

    return element;
}

"use strict";

function CBSectionListView(list)
{
    this._list      = list;
    this._element   = document.createElement("div");

    var handler
    this._list.forEach(this.appendSectionEditorCallback());
}

CBSectionListView.prototype.element = function()
{
    return this._element;
};

CBSectionListView.prototype.appendSectionEditorCallback = function()
{
    var self        = this;
    var callback    = function(sectionModel, index, array)
    {
        self.appendSectionEditorForModel(sectionModel);
    };

    return callback;
};

CBSectionListView.prototype.appendSectionEditorForModel = function(sectionModel)
{
    var sectionEditorView = new CBSectionEditorView(sectionModel);

    this._element.appendChild(sectionEditorView.element());
};


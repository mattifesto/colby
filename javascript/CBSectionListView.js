"use strict";

function CBSectionListView(list)
{
    this._list      = list;
    this._element   = document.createElement("div");
    this._element.classList.add("CBSectionListView");

    this._list.forEach(this.displaySectionCallback());

    // TODO: Add UI for adding a section to the end of the list
}

CBSectionListView.prototype.element = function()
{
    return this._element;
};

/**
 * @return void
 */
CBSectionListView.prototype.displaySection = function(sectionModel)
{
    var sectionListItemView = this.newSectionListItemViewForModel(sectionModel);
    this._element.appendChild(sectionListItemView);
};

/**
 * @return function
 */
CBSectionListView.prototype.displaySectionCallback = function()
{
    var self                    = this;
    var displaySectionCallback  = function(sectionModel, index, array)
    {
        self.displaySection(sectionModel);
    };

    return displaySectionCallback;
};

/**
 * @return void
 */
CBSectionListView.prototype.deleteSection = function(sectionModel)
{
    var index = this._list.indexOf(sectionModel);

    if (-1 == index)
    {
        return;
    }

    this._list.splice(index, 1);

    var sectionListItemID = "CBSectionListItemView-" + sectionModel.sectionID;
    var sectionListItem     = document.getElementById(sectionListItemID);
    sectionListItem.parentNode.removeChild(sectionListItem);


    CBPageEditor.requestSave();
};

/**
 * @return function
 */
CBSectionListView.prototype.deleteSectionCallback = function(sectionModel)
{
    var self                    = this;
    var deleteSectionCallback   = function()
    {
        self.deleteSection(sectionModel);
    };

    return deleteSectionCallback;
};

/**
 * @return void
 */
CBSectionListView.prototype.insertSection = function(sectionModel, beforeSectionModel)
{
    var index = this._list.indexOf(beforeSectionModel);

    if (-1 == index)
    {
        return;
    }

    /**
     *
     */

    this._list.splice(index, 0, sectionModel);

    /**
     *
     */

    var beforeSectionListItemID     = "CBSectionListItemView-" + beforeSectionModel.sectionID;
    var beforeSectionListItemView   = document.getElementById(beforeSectionListItemID);
    var sectionListItemView         = this.newSectionListItemViewForModel(sectionModel);
    beforeSectionListItemView.parentNode.insertBefore(sectionListItemView, beforeSectionListItemView);


    CBPageEditor.requestSave();
};

/**
 * @return function
 */
CBSectionListView.prototype.insertSectionCallback = function(selectionControl, beforeSectionModel)
{
    var self                    = this;
    var insertSectionCallback   = function()
    {
        var sectionTypeID           = selectionControl.value();
        var sectionModelJSON        = CBSectionDescriptors[sectionTypeID].modelJSON;
        var sectionModel            = JSON.parse(sectionModelJSON);
        sectionModel.sectionID      = Colby.random160();

        self.insertSection(sectionModel, beforeSectionModel);
    };

    return insertSectionCallback;
};

/**
 * @return Element
 */
CBSectionListView.prototype.newSectionInsertionViewForModel = function(sectionModel)
{
    var sectionInsertionView                = document.createElement("div");
    sectionInsertionView.style.textAlign    = "center";

    /**
     *
     */

    var selectionControl    = new CBSelectionControl("insert ");
    sectionInsertionView.appendChild(selectionControl.rootElement());

    for (var sectionTypeID in CBSectionDescriptors)
    {
        selectionControl.appendOption(sectionTypeID, CBSectionDescriptors[sectionTypeID].name);
    }

    /**
     *
     */

    var insertButton            = document.createElement("button");
    var insertSectionCallback   = this.insertSectionCallback(selectionControl, sectionModel);
    insertButton.addEventListener('click', insertSectionCallback, false);
    insertButton.appendChild(document.createTextNode("Insert"));
    sectionInsertionView.appendChild(insertButton);


    return sectionInsertionView;
};

/**
 * @return Element
 */
CBSectionListView.prototype.newSectionListItemViewForModel = function(model)
{
    if (!model.sectionID)
    {
        model.sectionID = Colby.random160();
    }

    var sectionListItemView    = document.createElement("div");
    sectionListItemView.id     = "CBSectionListItemView-" + model.sectionID;

    /**
     *
     */

    var sectionInsertionView = this.newSectionInsertionViewForModel(model);
    sectionListItemView.appendChild(sectionInsertionView);

    /**
     *
     */

    var sectionEditorView = new CBSectionEditorView(model, this);
    sectionListItemView.appendChild(sectionEditorView.element());


    return sectionListItemView;
};

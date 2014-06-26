"use strict";

function CBSectionListView(list)
{
    this._list      = list;
    this._element   = document.createElement("div");
    this._element.classList.add("CBSectionListView");

    /**
     * Add all of the sections already in the list to the view.
     */

    this._list.forEach(this.displaySectionCallback());

    /**
     * Selection control to add a new section to the end of the section list.
     */

    var appendSectionSelectionControl = new CBSectionSelectionControl();
    appendSectionSelectionControl.setAction(this, this.appendSection);

    this._element.appendChild(appendSectionSelectionControl.element());
}

CBSectionListView.prototype.appendSection = function(sender)
{
    if ("function" == typeof window[sender.value()])
    {
        var viewClassName   = sender.value();
        var viewConstructor = window[viewClassName];
        var view            = new viewConstructor();

        this._list.push(view.model);

        /**
         * Appending this view as the last view means inserting it before
         * the new view selection element.
         */

        var viewListItemElement             = this.createViewListItemElementForView(view);
        var appendSectionSelectionElement   = this._element.lastChild;
        this._element.insertBefore(viewListItemElement, appendSectionSelectionElement);
    }
    else
    {
        var sectionTypeID           = sender.value();
        var sectionModelJSON        = CBSectionDescriptors[sectionTypeID].modelJSON;
        var sectionModel            = JSON.parse(sectionModelJSON);
        sectionModel.sectionID      = Colby.random160();

        /**
         *
         */

        this._list.push(sectionModel);

        /**
         *
         */

        var sectionListItemView             = this.newSectionListItemViewForModel(sectionModel);
        var appendSectionSelectionElement   = this._element.lastChild;
        this._element.insertBefore(sectionListItemView, appendSectionSelectionElement);
    }

    CBPageEditor.requestSave();
};

/**
 * @return Element
 */
CBSectionListView.prototype.createViewListItemElementForView = function(view)
{
    var viewListItemElement    = document.createElement("div");
    viewListItemElement.id     = "CBSectionListItemView-" + view.model.ID;
    viewListItemElement.style.backgroundColor = "blue";

    /**
     *
     */

    var sectionSelectionControl                 = new CBSectionSelectionControl();
    sectionSelectionControl.insertBeforeModel   = view.model;
    sectionSelectionControl.setAction(this, this.insertSection);
    viewListItemElement.appendChild(sectionSelectionControl.element());

    /**
     * TODO: insert code taken from CBSectionEditorView to fill out the element.
     */

    //var sectionEditorView = new CBSectionEditorView(model, this);
    //viewListItemElement.appendChild(sectionEditorView.element());


    return viewListItemElement;
}

/**
 * @return Element
 */
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
CBSectionListView.prototype.insertSection = function(sender)
{
    var index = this._list.indexOf(sender.insertBeforeModel);

    if (-1 == index)
    {
        return;
    }

    var sectionTypeID           = sender.value();
    var sectionModelJSON        = CBSectionDescriptors[sectionTypeID].modelJSON;
    var sectionModel            = JSON.parse(sectionModelJSON);
    sectionModel.sectionID      = Colby.random160();

    /**
     *
     */

    this._list.splice(index, 0, sectionModel);

    /**
     *
     */

    var beforeSectionListItemViewID = "CBSectionListItemView-" + sender.insertBeforeModel.sectionID;
    var beforeSectionListItemView   = document.getElementById(beforeSectionListItemViewID);
    var sectionListItemView         = this.newSectionListItemViewForModel(sectionModel);
    beforeSectionListItemView.parentNode.insertBefore(sectionListItemView, beforeSectionListItemView);


    CBPageEditor.requestSave();
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

    var sectionSelectionControl                 = new CBSectionSelectionControl();
    sectionSelectionControl.insertBeforeModel   = model;
    sectionSelectionControl.setAction(this, this.insertSection);
    sectionListItemView.appendChild(sectionSelectionControl.element());

    /**
     *
     */

    var sectionEditorView = new CBSectionEditorView(model, this);
    sectionListItemView.appendChild(sectionEditorView.element());


    return sectionListItemView;
};

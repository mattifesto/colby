"use strict";


/**
 *
 */
function CBSectionListView(list)
{
    this._list      = list;
    this._element   = document.createElement("div");
    this._element.classList.add("CBSectionListView");

    /**
     * Add all of the sections already in the list to the view.
     */

    this._list.forEach(this.displaySection.bind(this));

    /**
     * View menu to append a new view to the container.
     */

    var viewMenu        = CBViewMenu.menu();
    viewMenu.callback   = this.appendSection.bind(this, viewMenu);

    this._element.appendChild(viewMenu.element());
}

/**
 *
 */
CBSectionListView.prototype.appendSection = function(viewMenu)
{
    var appendSectionSelectionElement;

    var viewEditorClassName = viewMenu.value() + "Editor";

    if ("object" == typeof window[viewEditorClassName])
    {
        var viewEditorClass     = window[viewEditorClassName];
        var viewEditor          = Object.create(viewEditorClass).init();

        this._list.push(viewEditor.model);

        /**
         * Appending this view as the last view means inserting it before
         * the new view selection element.
         */

        var viewListItemElement             = this.createViewListItemElementForViewEditor(viewEditor);
        appendSectionSelectionElement       = this._element.lastChild;
        this._element.insertBefore(viewListItemElement, appendSectionSelectionElement);
    }
    else
    {
        var sectionTypeID           = viewMenu.value();
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
        appendSectionSelectionElement       = this._element.lastChild;
        this._element.insertBefore(sectionListItemView, appendSectionSelectionElement);
    }

    CBPageEditor.requestSave();
};

/**
 * @return Element
 */
CBSectionListView.prototype.createViewListItemElementForViewEditor = function(viewEditor)
{
    var viewListItemElement    = document.createElement("div");
    viewListItemElement.id     = "CBSectionListItemView-" + viewEditor.model.ID;

    /**
     *
     */

    var viewMenu                    = CBViewMenu.menu();
    viewMenu.modelToInsertBefore    = viewEditor.model;
    viewMenu.callback               = this.insertSection.bind(this, viewMenu);

    viewListItemElement.appendChild(viewMenu.element());

    /**
     * TODO: insert code taken from CBSectionEditorView to fill out the element.
     */

    var editorElement = document.createElement("section");
    editorElement.id        = "s" + viewEditor.model.sectionID;
    editorElement.classList.add("CBSectionEditorView");

    var header              = document.createElement("header");
    header.textContent = viewEditor.model.className;
    editorElement.appendChild(header);


    var deleteButton            = document.createElement("button");
    var deleteSectionCallback   = this.deleteSection.bind(this, viewEditor.model);
    deleteButton.addEventListener('click', deleteSectionCallback, false);
    deleteButton.appendChild(document.createTextNode("Delete Section"));
    header.appendChild(deleteButton);


    var innerElement  = document.createElement("div");
    editorElement.appendChild(innerElement);

    innerElement.appendChild(viewEditor.element());

    viewListItemElement.appendChild(editorElement);

    return viewListItemElement;
};

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
CBSectionListView.prototype.displaySection = function(model, index, modelArray)
{
    var viewEditor;
    var viewListItemElement;

    /**
     * Translate deprecated views into non-deprecated views.
     */

    if (model.sectionTypeID)
    {
        switch (model.sectionTypeID)
        {
            /**
             * Translate CBBackgroundSection to CBBackgroundView.
             */

            case "c4bacd7cf5315e5a07c20072cbb0f355bdb4b8bc":

                if ("object" == typeof CBBackgroundViewEditor)
                {
                    viewEditor          = Object.create(CBBackgroundViewEditor);
                    viewEditor.initWithModel(model);

                    model               = viewEditor.model;
                    modelArray[index]   = model;
                }

                break;

            /**
             * Translate PMImageSection to LEImageView
             */

            case "85ad8d3561e980afffc4847803ce83e7aed6af6b":

                if ("object" == typeof LEImageViewEditor)
                {
                    viewEditor          = Object.create(LEImageViewEditor);
                    viewEditor.initWithModel(model);

                    model               = viewEditor.model;
                    modelArray[index]   = model;
                }

                break;

            default:

                break;
        }
    }

    /**
     * Display
     */
    if (model.className)
    {
        var viewEditorClassName = model.className + "Editor";
        var viewEditorClass     = window[viewEditorClassName];
        viewEditor              = Object.create(viewEditorClass).initWithModel(model);
        viewListItemElement     = this.createViewListItemElementForViewEditor(viewEditor);
    }
    else
    {
        viewListItemElement     = this.newSectionListItemViewForModel(model);
    }

    this._element.appendChild(viewListItemElement);
};

/**
 * @return void
 */
CBSectionListView.prototype.deleteSection = function(sectionModel)
{
    var ID;
    var index = this._list.indexOf(sectionModel);

    if (-1 == index)
    {
        return;
    }

    this._list.splice(index, 1);

    if (sectionModel.ID)
    {
        ID = sectionModel.ID;
    }
    else
    {
        ID = sectionModel.sectionID;
    }

    var sectionListItemID = "CBSectionListItemView-" + ID;
    var sectionListItem     = document.getElementById(sectionListItemID);
    sectionListItem.parentNode.removeChild(sectionListItem);


    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBSectionListView.prototype.insertSection = function(viewMenu)
{
    var index = this._list.indexOf(viewMenu.modelToInsertBefore);

    if (-1 == index)
    {
        return;
    }

    var beforeSectionModelID        = viewMenu.modelToInsertBefore.ID ?
                                      viewMenu.modelToInsertBefore.ID :
                                      viewMenu.modelToInsertBefore.sectionID;

    var beforeSectionListItemViewID = "CBSectionListItemView-" + beforeSectionModelID;
    var beforeSectionListItemView   = document.getElementById(beforeSectionListItemViewID);
    var viewEditorClassName         = viewMenu.value() + "Editor";

    if ("object" == typeof window[viewEditorClassName])
    {
        var viewEditorClass     = window[viewEditorClassName];
        var viewEditor          = Object.create(viewEditorClass).init();

        /**
         *
         */

        this._list.splice(index, 0, viewEditor.model);

        /**
         * Appending this view as the last view means inserting it before
         * the new view selection element.
         */

        var viewListItemElement = this.createViewListItemElementForViewEditor(viewEditor);
        beforeSectionListItemView.parentNode.insertBefore(viewListItemElement, beforeSectionListItemView);
    }
    else
    {
        var sectionTypeID           = viewMenu.value();
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

        var sectionListItemView         = this.newSectionListItemViewForModel(sectionModel);
        beforeSectionListItemView.parentNode.insertBefore(sectionListItemView, beforeSectionListItemView);
    }

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

    var viewMenu                    = CBViewMenu.menu();
    viewMenu.modelToInsertBefore    = model;
    viewMenu.callback               = this.insertSection.bind(this, viewMenu);

    sectionListItemView.appendChild(viewMenu.element());

    /**
     *
     */

    var sectionEditorView = new CBSectionEditorView(model, this);
    sectionListItemView.appendChild(sectionEditorView.element());


    return sectionListItemView;
};

"use strict";


var CBModelArrayEditor = {}

/**
 * @return instance type
 */
CBModelArrayEditor.editorForModelArray = function(modelArray) {

    var editor = Object.create(CBModelArrayEditor);

    editor.initWithModelArray(modelArray);

    return editor;
};

/**
 * @return void
 */
CBModelArrayEditor.initWithModelArray = function(modelArray) {

    this.modelArray      = modelArray;
    this._element   = document.createElement("div");
    this._element.classList.add("CBModelArrayEditor");

    /**
     * Add all of the sections already in the list to the view.
     */

    this.modelArray.forEach(this.displayViewEditor.bind(this));

    /**
     * View menu to append a new view to the container.
     */

    var viewMenu        = CBViewMenu.menu();
    viewMenu.callback   = this.insertView.bind(this, viewMenu);

    this._element.appendChild(viewMenu.element());
};

/**
 * @return Element
 */
CBModelArrayEditor.element = function() {

    return this._element;
};

/**
 * @return void
 */
CBModelArrayEditor.displayViewEditor = function(model, index, modelArray) {

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
     *
     */

    var viewMenu                    = CBViewMenu.menu();
    viewMenu.callback               = this.insertView.bind(this, viewMenu);

    this._element.appendChild(viewMenu.element());

    /**
     * Display
     */
    if (model.className)
    {
        viewEditor                  = CBViewEditor.editorForViewModel(model);
        viewEditor.deleteCallback   = this.deleteView.bind(this, viewEditor);

        this._element.appendChild(viewEditor.outerElement());
    }
    else
    {
        /**
         * TODO: Upgrade the CBViewEditor code to create a default editor for
         * models it doesn't recognize and remove this else block.
         *
         * This will also allow the removal of the code in CBView.php that
         * creates a mini custom editor where one isn't define and allow
         * subclasses to call CBView::includeEditorDependencies.
         */
    }
};

/**
 * @return void
 */
CBModelArrayEditor.deleteView = function(viewEditor) {

    var viewModel   = viewEditor.model;
    var index       = this.modelArray.indexOf(viewModel);

    if (-1 == index)
    {
        return;
    }

    var viewMenuElement = viewEditor.outerElement().previousSibling;

    this._element.removeChild(viewMenuElement);
    this._element.removeChild(viewEditor.outerElement());

    this.modelArray.splice(index, 1);

    CBPageEditor.requestSave();
};

/**
 * @return void
 */
CBModelArrayEditor.insertView = function(viewMenu) {

    var elementToInsertBefore   = viewMenu.element();

    /**
     *
     */

    var newViewMenu         = CBViewMenu.menu();
    newViewMenu.callback    = this.insertView.bind(this, newViewMenu);

    this._element.insertBefore(newViewMenu.element(), elementToInsertBefore);

    /**
     *
     */

    var viewEditor              = CBViewEditor.editorForViewClassName(viewMenu.value());
    viewEditor.deleteCallback   = this.deleteView.bind(this, viewEditor);

    this._element.insertBefore(viewEditor.outerElement(), elementToInsertBefore);

    /**
     *
     */

    if (viewMenu.element().nextSibling)
    {
        var modelToInsertBefore     = viewMenu.element().nextSibling.model;

        var index = this.modelArray.indexOf(modelToInsertBefore);

        if (-1 == index)
        {
            return;
        }

        this.modelArray.splice(index, 0, viewEditor.model);
    }
    else
    {
        this.modelArray.push(viewEditor.model);
    }

    CBPageEditor.requestSave();
};

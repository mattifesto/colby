"use strict";


var CBModelArrayEditor = {

    /**
     * @param {Array} array
     *
     * @return {Element}
     */
    createEditor : function(args) {
        var element     = document.createElement("div");
        var specArray   = args.array;

        element.classList.add("CBModelArrayEditor");

        /**
         * Upgrade specs
         */

        specArray.forEach(CBModelArrayEditor.upgradeSpec);

        /**
         * Add all of the sections already in the list to the view.
         */

        specArray.forEach(function(spec) {
            var menuElement         = CBModelArrayEditor.createMenuElement({
                containerElement    : element,
                spec                : spec,
                specArray           : specArray });

            var viewEditorWidget    = CBModelArrayEditor.createViewEditorWidget({
                containerElement    : element,
                spec                : spec,
                specArray           : specArray });

            element.appendChild(menuElement);
            element.appendChild(viewEditorWidget);

            return element;
        });

        /**
         * View menu to append a new view to the container.
         */

        var menuElement         = CBModelArrayEditor.createMenuElement({
            containerElement    : element,
            spec                : undefined,
            specArray           : specArray });

        element.appendChild(menuElement);

        return element;
    },

    /**
     * @param {Object}  beforeSpec
     * @param {Element} containerElement
     * @param {Object}  menuState
     * @param {Array}   specArray
     *
     * @return void
     */
    handleInsertRequested : function(args) {
        var spec    = { "className" : args.menuState.selectedViewClassName };
        var index   = args.beforeSpec ? args.specArray.indexOf(args.beforeSpec) : args.specArray.length;

        if (-1 == index) {
            throw "View specification not found in list.";
        }

        args.specArray.splice(index, 0, spec);

        var menuElement         = CBModelArrayEditor.createMenuElement({
            containerElement    : args.containerElement,
            spec                : spec,
            specArray           : args.specArray });

        var viewEditorWidget    = CBModelArrayEditor.createViewEditorWidget({
            containerElement    : args.containerElement,
            spec                : spec,
            specArray           : args.specArray });

        var beforeElement       = args.containerElement.children[index * 2];

        args.containerElement.insertBefore(menuElement,         beforeElement);
        args.containerElement.insertBefore(viewEditorWidget,    beforeElement);

        CBPageEditor.requestSave();
    },

    /**
     * @param {Element} containerElement
     * @param {Object}  spec
     * @param {Array}   specArray
     *
     * @return {Element}
     */
    createMenuElement : function(args) {
        var menuState               = {};

        var handleInsertRequested   = CBModelArrayEditor.handleInsertRequested.bind(undefined, {
            beforeSpec              : args.spec,
            containerElement        : args.containerElement,
            menuState               : menuState,
            specArray               : args.specArray });

        var menuElement             = CBViewMenu.createMenu({
            handleInsertRequested   : handleInsertRequested,
            menuState               : menuState });

        return menuElement;
    },

    /**
     * @param {Element} containerElement
     * @param {Object}  spec
     * @param {Array}   specArray
     *
     * @return {Element}
     */
    createViewEditorWidget : function(args) {
        var handleViewDeleted   = CBModelArrayEditor.removeSpec.bind(undefined, {
            array               : args.specArray,
            editor              : args.containerElement,
            spec                : args.spec });

        var viewEditorWidget    = CBViewEditorWidgetFactory.createWidget({
            spec                : args.spec,
            handleViewDeleted   : handleViewDeleted });

        return viewEditorWidget;
    },

    /**
     * @param {Array}   array
     * @param {Element} editor
     * @param {Object}  spec
     *
     * @return void
     */
    removeSpec : function(args) {
        var index = args.array.indexOf(args.spec);

        if (-1 == index) {
            throw "View specification not found in list.";
        }

        // Remove both the insert menu and the editor widget.
        args.editor.removeChild(args.editor.children[index * 2]);
        args.editor.removeChild(args.editor.children[index * 2]);

        args.array.splice(index, 1);

        CBPageEditor.requestSave();
    },

    /**
     * @return void
     */
    upgradeSpec : function(model, index, modelArray) {
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
    }
};

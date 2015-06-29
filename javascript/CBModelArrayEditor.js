"use strict";

/**
 * @deprecated use CBSpecArrayEditor instead
 *
 * 2015.06.28 This class is easily replaced by CBSpecArrayEditor with better
 * results so the replacements should happen soon and this class should be
 * deleted.
 */
var CBModelArrayEditor = {

    /**
     * @param {function}    handleSpecChanged
     * @param {Array}       specArray
     *
     * @return {Element}
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBModelArrayEditor";

        /**
         * Upgrade specs
         */

        args.specArray.forEach(CBModelArrayEditor.upgradeSpec);

        /**
         * Add all of the sections already in the list to the view.
         */

        args.specArray.forEach(function(spec) {
            var menuElement         = CBModelArrayEditor.createMenuElement({
                containerElement    : element,
                handleSpecChanged   : args.handleSpecChanged,
                spec                : spec,
                specArray           : args.specArray });

            var viewEditorWidget    = CBModelArrayEditor.createViewEditorWidget({
                containerElement    : element,
                handleSpecChanged   : args.handleSpecChanged,
                spec                : spec,
                specArray           : args.specArray });

            element.appendChild(menuElement);
            element.appendChild(viewEditorWidget);

            return element;
        });

        /**
         * View menu to append a new view to the container.
         */

        var menuElement         = CBModelArrayEditor.createMenuElement({
            containerElement    : element,
            handleSpecChanged   : args.handleSpecChanged,
            spec                : undefined,
            specArray           : args.specArray });

        element.appendChild(menuElement);

        return element;
    },

    /**
     * @param {Object}      beforeSpec
     * @param {Element}     containerElement
     * @param {function}    handleSpecChanged
     * @param {Object}      menuState
     * @param {Array}       specArray
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
            handleSpecChanged   : args.handleSpecChanged,
            spec                : spec,
            specArray           : args.specArray });

        var viewEditorWidget    = CBModelArrayEditor.createViewEditorWidget({
            containerElement    : args.containerElement,
            handleSpecChanged   : args.handleSpecChanged,
            spec                : spec,
            specArray           : args.specArray });

        var beforeElement       = args.containerElement.children[index * 2];

        args.containerElement.insertBefore(menuElement,         beforeElement);
        args.containerElement.insertBefore(viewEditorWidget,    beforeElement);

        args.handleSpecChanged.call();
    },

    /**
     * @param {Element}     containerElement
     * @param {function}    handleSpecChanged
     * @param {Object}      spec
     * @param {Array}       specArray
     *
     * @return {Element}
     */
    createMenuElement : function(args) {
        var menuState               = {};

        var handleInsertRequested   = CBModelArrayEditor.handleInsertRequested.bind(undefined, {
            beforeSpec              : args.spec,
            containerElement        : args.containerElement,
            handleSpecChanged       : args.handleSpecChanged,
            menuState               : menuState,
            specArray               : args.specArray });

        var menuElement             = CBViewMenu.createMenu({
            handleInsertRequested   : handleInsertRequested,
            menuState               : menuState });

        return menuElement;
    },

    /**
     * @param {Element}     containerElement
     * @param {function}    handleSpecChanged
     * @param {Object}      spec
     * @param {Array}       specArray
     *
     * @return {Element}
     */
    createViewEditorWidget : function(args) {
        var handleViewDeleted   = CBModelArrayEditor.removeSpec.bind(undefined, {
            editor              : args.containerElement,
            handleSpecChanged   : args.handleSpecChanged,
            spec                : args.spec,
            specArray           : args.specArray });

        var viewEditorWidget    = CBViewEditorWidgetFactory.createWidget({
            spec                : args.spec,
            handleViewDeleted   : handleViewDeleted });

        return viewEditorWidget;
    },

    /**
     * @param {Element}     editor
     * @param {function}    handleSpecChanged
     * @param {Object}      spec
     * @param {Array}       specArray
     *
     * @return void
     */
    removeSpec : function(args) {
        var index = args.specArray.indexOf(args.spec);

        if (-1 == index) {
            throw "View specification not found in list.";
        }

        // Remove both the insert menu and the editor widget.
        args.editor.removeChild(args.editor.children[index * 2]);
        args.editor.removeChild(args.editor.children[index * 2]);

        args.specArray.splice(index, 1);

        args.handleSpecChanged.call();
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

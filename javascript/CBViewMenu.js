"use strict";

var CBViewMenu = {

    /**
     * This element will populate the `selectedViewClassName` property of the
     * `menuState` object with the current selected menu item's value.
     *
     * @param {Object}      menuState
     * @param {function}    handleInsertRequested
     *
     * @return {Element}
     */
    createMenu : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBViewMenu";
        var select          = document.createElement("select");
        var button          = document.createElement("button");
        button.textContent  = "Insert View";
        var line            = document.createElement("div");

        var handler = CBViewMenu.handleInsertButtonClicked.bind(undefined, {
            handleInsertRequested   : args.handleInsertRequested,
            menuState               : args.menuState,
            selectElement           : select });

        button.addEventListener("click", handler);

        CBPageEditorAvailableViewClassNames.forEach(function(className) {
            var option          = document.createElement("option");
            option.textContent  = className;
            option.value        = className;

            select.appendChild(option);
        });

        args.menuState.selectedViewClassName = select.value;

        element.appendChild(line);
        element.appendChild(select);
        element.appendChild(button);

        return element;
    },

    /**
     * @param {function}    handleInsertRequested
     * @param {Object}      menuState
     * @param {Element}     selectElement
     *
     * @return void
     */
    handleInsertButtonClicked : function(args) {
        args.menuState.selectedViewClassName = args.selectElement.value;

        args.handleInsertRequested.call();
    }
};

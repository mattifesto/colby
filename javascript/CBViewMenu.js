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

/**
 * @return instance type
 */
CBViewMenu.menu = function() {

    var menu = Object.create(CBViewMenu);

    menu.init();

    return menu;
};

/**
 * This control is used exclusively by the CBModelArrayEditor object and it
 * it contains behavior and styling specific to that use.
 */
CBViewMenu.init = function() {

    var option;

    this._element           = document.createElement("div");
    this._element.className = "CBViewMenu";
    this._select            = document.createElement("select");
    var button              = document.createElement("button");
    button.textContent      = "Insert View";
    var line                = document.createElement("div");

    var listener = this.insertView.bind(this);
    button.addEventListener("click", listener);

    this._element.appendChild(line);
    this._element.appendChild(this._select);
    this._element.appendChild(button);

    /**
     *
     */

    for (var i = 0; i < CBPageEditorAvailableViewClassNames.length; i++)
    {
        var viewClassName   = CBPageEditorAvailableViewClassNames[i];
        option              = document.createElement("option");
        option.textContent  = viewClassName;
        option.value        = viewClassName;

        this._select.appendChild(option);
    }
};

/**
 * @return Element
 */
CBViewMenu.element = function() {

    return this._element;
};

/**
 * @return void
 */
CBViewMenu.insertView = function() {

    this.callback();
};

/**
 * @return string
 */
CBViewMenu.value = function() {

    return this._select.value;
};

"use strict";

var CBViewMenu = {};

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
    button.textContent      = "Insert Section";
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

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
 * This control is used exclusively by the CBSectionListView object and it
 * it contains behavior and styling specific to that use.
 */
CBViewMenu.init = function() {

    var option;

    this._element           = document.createElement("div");
    this._element.className = "CBViewMenu";
    this._select            = document.createElement("select");
    this._button            = document.createElement("button");
    var line                = document.createElement("div");

    this._element.appendChild(line);
    this._element.appendChild(this._select);
    this._element.appendChild(this._button);
    this._button.appendChild(document.createTextNode("Insert Section"));

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
CBViewMenu.setAction = function(target, method) {

    if (this._action)
    {
        this._button.removeEventListener('click', this._action, false);
    }

    var sender      = this;
    this._action    = function()
    {
        method.call(target, sender);
    };

    this._button.addEventListener('click', this._action, false);
};

/**
 * @return string
 */
CBViewMenu.value = function() {

    return this._select.value;
};

"use strict";

var CBPagesListViewController =
{
    element : null,
    table   : null
};

/**
 * @return instance type
 */
CBPagesListViewController.initWithElement = function(element)
{
    this.element  = element;
    this.table    = element.getElementsByTagName("table")[0];

    return this;
};

/**
 * @return instance type
 */
CBPagesListViewController.controllerForElement = function(element)
{
    if (element.controller)
    {
        if (CBPagesListViewController.isPrototypeOf(element.controller))
        {
            return element.controller;
        }
        else
        {
            throw "The controller for the element is not of the expected type.";
        }
    }
    else
    {
        element.controller = Object.create(CBPagesListViewController).initWithElement(element);

        return element.controller;
    }
};

/**
 * @return void
 */
CBPagesListViewController.addListItem = function(listItem)
{
    var tr = document.createElement("tr");
    var td = document.createElement("td");
    td.textContent = "testing";
    tr.appendChild(td);

    this.table.tBodies[0].appendChild(tr);
};

/**
 * @return void
 */
CBPagesListViewController.DOMContentDidLoad = function()
{
    var elements = document.getElementsByClassName("CBPagesListView");

    for (var i = 0; i < elements.length; i++)
    {
        var element = elements[i];

        element.controller = this.controllerForElement(element);
    }
};

/**
 *
 */
(function()
{
    var listener = CBPagesListViewController.DOMContentDidLoad.bind(CBPagesListViewController);

    document.addEventListener("DOMContentLoaded", listener);
})();

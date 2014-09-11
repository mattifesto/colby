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
    if (element.controller)
    {
        throw "This element already has a controller.";
    }

    var controller      = Object.create(CBPagesListViewController);
    controller.element  = element;
    controller.table    = element.getElementsByTagName("table")[0];

    return controller;
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

        if (!element.controller)
        {
            element.controller = CBPagesListViewController.initWithElement(element);
        }
    }
};

(function()
{
    var listener = CBPagesListViewController.DOMContentDidLoad.bind(CBPagesListViewController);

    document.addEventListener("DOMContentLoaded", listener);
})();

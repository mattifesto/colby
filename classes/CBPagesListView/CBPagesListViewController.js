"use strict";

var CBPagesListViewController =
{
    element : null
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

    var test            = document.createElement("div");
    test.textContent    = "Test Text";
    element.appendChild(test);

    return controller;
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

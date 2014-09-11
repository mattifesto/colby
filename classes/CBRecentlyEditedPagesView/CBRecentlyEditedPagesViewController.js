"use strict";

var CBRecentlyEditedPagesViewController =
{
    element             : null,
    listViewController  : null
};

/**
 * @return instance type
 */
CBRecentlyEditedPagesViewController.initWithElement = function(element)
{
    if (element.controller)
    {
        throw "This element already has a controller.";
    }

    var controller      = Object.create(CBRecentlyEditedPagesViewController);
    controller.element  = element;

    var listView        = element.getElementsByClassName("CBPagesListView")[0];

    if (!listView.controller)
    {
        var listViewController  = CBPagesListViewController.initWithElement(listView);
        listView.controller     = listViewController;
    }

    controller.listViewController = listView.controller;

    controller.listViewController.addListItem();
    controller.listViewController.addListItem();
    controller.listViewController.addListItem();

    return controller;
};

/**
 * @return void
 */
CBRecentlyEditedPagesViewController.DOMContentDidLoad = function()
{
    var elements = document.getElementsByClassName("CBRecentlyEditedPagesView");

    for (var i = 0; i < elements.length; i++)
    {
        var element = elements[i];

        if (!element.controller)
        {
            element.controller = CBRecentlyEditedPagesViewController.initWithElement(element);
        }
    }
};

(function()
{
    var object      = CBRecentlyEditedPagesViewController;
    var listener    = object.DOMContentDidLoad.bind(object);

    document.addEventListener("DOMContentLoaded", listener);
})();

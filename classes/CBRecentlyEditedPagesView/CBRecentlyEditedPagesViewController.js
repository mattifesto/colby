"use strict";

var CBRecentlyEditedPagesViewController =
{
    element             : null,
    listViewController  : null,
    refreshListTimer    : null
};

/**
 * @return instance type
 */
CBRecentlyEditedPagesViewController.initWithElement = function(element)
{
    this.element            = element;
    var listView            = element.getElementsByClassName("CBPagesListView")[0];
    this.listViewController = CBPagesListViewController.controllerForElement(listView);
    this.refreshListTimer   = Object.create(CBDelayTimer).init();

    this.listViewController.addListItem();
    this.listViewController.addListItem();
    this.listViewController.addListItem();

    return this;
};

/**
 * @return instance type
 */
CBRecentlyEditedPagesViewController.controllerForElement = function(element)
{
    if (element.controller)
    {
        if (CBRecentlyEditedPagesViewController.isPrototypeOf(element.controller))
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
        element.controller = Object.create(CBRecentlyEditedPagesViewController).initWithElement(element);

        return element.controller;
    }
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

        element.controller = this.controllerForElement(element);
    }
};

/**
 *
 */
(function()
{
    var object      = CBRecentlyEditedPagesViewController;
    var listener    = object.DOMContentDidLoad.bind(object);

    document.addEventListener("DOMContentLoaded", listener);
})();

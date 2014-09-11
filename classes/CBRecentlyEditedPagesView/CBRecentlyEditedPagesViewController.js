"use strict";

var CBRecentlyEditedPagesViewController =
{
    element             : null,
    listViewController  : null,
    refreshTimer        : null
};

/**
 * @return instance type
 */
CBRecentlyEditedPagesViewController.initWithElement = function(element)
{
    this.element            = element;
    var listView            = element.getElementsByClassName("CBPagesListView")[0];
    this.listViewController = CBPagesListViewController.controllerForElement(listView);

    this.refreshTimer                       = Object.create(CBDelayTimer).init();
    this.refreshTimer.callback              = this.refreshListData.bind(this);
    this.refreshTimer.delayInMilliseconds   = 20000;

    this.refreshListData();

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
 * @return instance type
 */
CBRecentlyEditedPagesViewController.refreshListData = function()
{
    var xhr = new XMLHttpRequest();
    xhr.onload  = this.refreshListDataDidComplete.bind(this, xhr);

    xhr.open("POST", "/admin/pages/api/get-recently-edited-pages/");
    xhr.send();
};

/**
 * @return instance type
 */
CBRecentlyEditedPagesViewController.refreshListDataDidComplete = function(xhr)
{
    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }

    this.listViewController.clear();

    for (var i = 0; i < response.pages.length; i++)
    {
        this.listViewController.addListItem(response.pages[i]);
    }

    Colby.beginUpdatingTimes();

    this.refreshTimer.restart();
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

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
 * @return void
 */
CBPagesListViewController.addListItem = function(listItem)
{
    var tr = document.createElement("tr");

    /**
     * Buttons
     */

    var td              = document.createElement("td");
    var button          = document.createElement("button");
    button.textContent  = "Edit";
    var editListener    = function() {

        location.href = "/admin/pages/edit/?data-store-id=" + listItem.dataStoreID;
    };

    button.addEventListener("click", editListener);

    td.appendChild(button);
    tr.appendChild(td);

    /**
     * Title
     */

    td = document.createElement("td");
    td.textContent = listItem.title;
    tr.appendChild(td);

    /**
     * Published
     */

    td = document.createElement("td");
    var span        = document.createElement("span");
    span.className  = "time";

    if (listItem.isPublished) {

        span.setAttribute("data-timestamp", listItem.publicationTimeStamp * 1000);
    }

    td.appendChild(span);
    tr.appendChild(td);

    /**
     * Updated
     */

    td              = document.createElement("td");
    span            = document.createElement("span");
    span.className  = "time";

    span.setAttribute("data-timestamp", listItem.updated * 1000);
    td.appendChild(span);
    tr.appendChild(td);

    this.table.tBodies[0].appendChild(tr);
};

/**
 * @return void
 */
CBPagesListViewController.clear = function()
{
    var tBody = this.table.tBodies[0];

    while (tBody.firstChild)
    {
        tBody.removeChild(tBody.firstChild);
    }
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

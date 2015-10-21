"use strict";

var CBModelListViewFactory = {

    /**
     * @param {function} fetchListItems
     *
     * @return {Element}
     */
    createElement : function(args) {
        var element = document.createElement("div");
        element.className = "CBModelListView";
        var container = document.createElement("div");
        container.className = "container";

        CBModelListViewFactory.fetchListItems({
            listElement : container,
            fetchListItems : args.fetchListItems,
        });

        element.appendChild(container);

        return element;
    },

    /**
     * @param {hex160} args.itemID
     * @param {string} args.itemTitle
     *
     * @return {Element}
     */
    createListItemElement : function(args) {
        var element = document.createElement("div");
        element.className = "CBModelListViewItem";
        element.textContent = args.itemTitle;

        element.addEventListener("click", CBModelListViewFactory.editModel.bind(undefined, {
            ID : args.itemID,
        }));

        return element;
    },

    /**
     * @param {hex160} args.ID
     *
     * @return undefined
     */
    editModel : function(args) {
        var URL = "/admin/models/edit/?ID=" + args.ID;

        window.location.href = URL;
    },

    /**
     * @param {Element} args.listElement
     * @param {function} args.fetchListItems
     *
     * @return undefined
     */
    fetchListItems : function(args) {
        var fetchListItemsDidLoad = CBModelListViewFactory.fetchListItemsDidLoad.bind(undefined, {
            listElement : args.listElement,
            fetchListItems : args.fetchListItems,
        });

        args.fetchListItems({
            fetchListItemsDidLoad : fetchListItemsDidLoad
        });
    },

    /**
     * @param {Element} args.listElement
     * @param {function} args.fetchListItems
     *
     * @return undefined
     */
    fetchListItemsDidLoad : function(args, listItems) {
        args.listElement.textContent = null;

        listItems.forEach(function(listItem) {
            args.listElement.appendChild(CBModelListViewFactory.createListItemElement({
                itemID : listItem.ID,
                itemTitle : listItem.title,
            }));
        });

        var callback = CBModelListViewFactory.fetchListItems.bind(undefined, args);

        window.setTimeout(callback, 30000);
    },
};

"use strict"; /* jshint strict: global */
/* globals CBUI, Colby */

var CBPageList = {

    /**
     * @param [object] pages
     *
     * @return Element
     */
    createElement: function(pages) {
        var element = document.createElement("div");
        element.className = "CBPageListView";

        var section = CBUI.createSection();

        pages.forEach(function (page) {
            section.appendChild(CBPageList.createPageSectionItem(page));
        });

        element.appendChild(section);

        return element;
    },

    /**
     * @param string page.className
     * @param hex160 page.ID
     * @param object page.keyValueData
     *
     * @return Element
     */
    createPageSectionItem: function (page) {
        var item = CBUI.createSectionItem2();

        /* description */
        var descriptionElement = document.createElement("div");
        descriptionElement.className = "title";
        descriptionElement.textContent = page.keyValueData.title;

        item.titleElement.appendChild(descriptionElement);

        item.titleElement.addEventListener("click", CBPageList.handlePageElementEditWasClicked.bind(undefined, {
            className: page.className,
            ID: page.ID,
        }));

        /* copy */
        var copyCommandElement = document.createElement("div");
        copyCommandElement.className = "command CBPageListCopyButton";
        copyCommandElement.textContent = "Copy";

        item.commandsElement.appendChild(copyCommandElement);

        copyCommandElement.addEventListener("click", CBPageList.handlePageElementCopyWasClicked.bind(undefined, {
            IDToCopy: page.ID,
        }));

        /* trash */
        var trashCommandElement = document.createElement("div");
        trashCommandElement.className = "command CBPageListTrashButton";
        trashCommandElement.textContent = "Trash";

        item.commandsElement.appendChild(trashCommandElement);

        trashCommandElement.addEventListener("click", CBPageList.handlePageElementTrashWasClicked.bind(undefined, {
            element: item,
            ID: page.ID,
        }));

        return item.element;
    },

    /**
     * @param {hex160} IDToCopy
     *
     * @return undefined
     */
    handlePageElementCopyWasClicked : function(args) {
        location.href = '/admin/pages/edit/?data-store-id=' +
                        Colby.random160() +
                        '&id-to-copy=' +
                        args.IDToCopy;
    },

    /**
     * @param string args.className
     * @param hex160 args.ID
     *
     * @return undefined
     */
    handlePageElementEditWasClicked : function (args) {
        if (args.className === "CBViewPage") {
            location.href = "/admin/pages/edit/?data-store-id=" + args.ID;
        } else {
            location.href = "/admin/page/?class=CBAdminPageForEditingModels&ID=" + args.ID;
        }
    },

    /**
     * @param {Element} element
     * @param {hex160} ID
     *
     * @return undefined
     */
    handlePageElementTrashWasClicked : function(args) {
        var formData = new FormData();
        formData.append("dataStoreID", args.ID);

        var xhr = new XMLHttpRequest();
        xhr.onload = CBPageList.handlePageElementTrashRequestDidLoad.bind(undefined, {
            element : args.element,
            xhr : xhr,
        });

        xhr.open("POST", "/admin/pages/api/move-to-the-trash/", true);
        xhr.send(formData);
    },

    /**
     * @param {Element} args.element
     * @param {XMLHttpRequest} args.xhr
     *
     * @return undefined
     */
    handlePageElementTrashRequestDidLoad : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.element.parentElement.removeChild(args.element);
        } else {
            Colby.displayResponse(response);
        }
    },
};

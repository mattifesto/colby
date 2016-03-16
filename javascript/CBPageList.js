"use strict";
/* globals CBUI, Colby */

var CBPageList = {

    /**
     * @param [object] pages
     *
     * @return Element
     */
    createElement : function(pages) {
        var element = document.createElement("div");
        element.className = "CBPageListView";

        var section = CBUI.createSection();

        pages.forEach(function (page) {
            var item = CBUI.createSectionItem();
            item.appendChild(CBPageList.createPageElement(page, item));
            section.appendChild(item);
        });

        element.appendChild(section);

        return element;
    },

    /**
     * @param string page.className
     * @param hex160 page.ID
     * @param object page.keyValueData
     *
     * @return {Element}
     */
    createPageElement : function (page, itemElement) {
        var element = document.createElement("div");
        element.className = "CBPageListPage";
        var title = document.createElement("div");
        title.className = "title";
        title.textContent = page.keyValueData.title;
        var copy = document.createElement("div");
        copy.className = "button copy";
        copy.textContent = "Copy";
        var trash = document.createElement("div");
        trash.className = "button trash";
        trash.textContent = "Trash";

        element.appendChild(title);
        element.appendChild(copy);
        element.appendChild(trash);

        title.addEventListener("click", CBPageList.handlePageElementEditWasClicked.bind(undefined, {
            className : page.className,
            ID : page.ID,
        }));

        copy.addEventListener("click", CBPageList.handlePageElementCopyWasClicked.bind(undefined, {
            IDToCopy : page.ID,
        }));

        trash.addEventListener("click", CBPageList.handlePageElementTrashWasClicked.bind(undefined, {
            element : itemElement,
            ID : page.ID,
        }));

        return element;
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
            location.href = "/admin/models/edit/?ID=" + args.ID;
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

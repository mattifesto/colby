"use strict";

var CBPageList = {

        selectedPageID : undefined,

        /**
         * @param [object] pages
         *
         * @return Element
         */
        createElement : function(pages) {
            var element = document.createElement("div");
            element.className = "CBPageListView";
            var list = document.createElement("div");
            list.className = "CBPageList";

            pages.forEach(function (page) {
                list.appendChild(CBPageList.createPageElement(page));
            });

            element.appendChild(list);

            return element;
        },

        /**
         * @param string page.className
         * @param hex160 page.ID
         * @param object page.keyValueData
         *
         * @return {Element}
         */
        createPageElement : function (page) {
            var element = document.createElement("div");
            element.className = "CBPageListPage";
            var title = document.createElement("div");
            title.className = "title";
            title.textContent = page.keyValueData.title;
            var edit = document.createElement("div");
            edit.className = "button edit";
            edit.textContent = "edit";
            var copy = document.createElement("div");
            copy.className = "button copy";
            copy.textContent = "copy";
            var trash = document.createElement("div");
            trash.className = "button trash";
            trash.textContent = "trash";

            element.appendChild(title);
            element.appendChild(edit);
            element.appendChild(copy);
            element.appendChild(trash);

            element.addEventListener("click", CBPageList.handlePageElementWasClicked.bind(undefined, {
                element : element,
                ID : page.ID,
            }));

            edit.addEventListener("click", CBPageList.handlePageElementEditWasClicked.bind(undefined, {
                className : page.className,
                ID : page.ID,
            }));

            copy.addEventListener("click", CBPageList.handlePageElementCopyWasClicked.bind(undefined, {
                IDToCopy : page.ID,
            }));

            trash.addEventListener("click", CBPageList.handlePageElementTrashWasClicked.bind(undefined, {
                element : element,
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

        /**
         * @param {Element} element
         * @param {string} ID
         *
         * @return undefined
         */
        handlePageElementWasClicked : function(args) {
            var elements = document.querySelectorAll(".CBPageListPage.selected");

            for (var i = 0; i < elements.length; i++) {
                elements[i].classList.remove("selected");
            }

            args.element.classList.add("selected");
            CBPageList.selectedPageID = args.ID;
        }
};
